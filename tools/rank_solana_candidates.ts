// SB Stats / Solana boosted-candidate ranking. Deno TypeScript; no Node or dependencies.
// Usage: deno run --allow-read --allow-write --allow-net=api.dexscreener.com tools/rank_solana_candidates.ts examples/solana/market/discovery/YYYY-MM-DD.json
type Candidate = { mint: string; claimed_url?: string | null; boost_amount?: number | null; total_boost_amount?: number | null; links_unverified?: unknown[] };
type Discovery = { schema: string; date_utc: string; candidates: Candidate[] };
type Pair = {
  chainId?: string; pairAddress?: string; dexId?: string; url?: string;
  baseToken?: { address?: string; name?: string; symbol?: string };
  quoteToken?: { address?: string; name?: string; symbol?: string };
  liquidity?: { usd?: number }; volume?: { h24?: number };
  txns?: { h24?: { buys?: number; sells?: number } };
  priceChange?: { h24?: number };
};
type Ranked = {
  rank: number | null; mint: string; name: string | null; symbol: string | null;
  eligible: boolean; score: number; liquidity_usd: number | null;
  volume_24h_usd: number | null; transactions_24h: number | null;
  price_change_24h_percent: number | null; pair_address: string | null; dex: string | null;
  boost_amount: number | null; total_boost_amount: number | null;
  telegram_claimed: boolean; telegram_verified: false; pair_url: string | null;
  claimed_url: string | null; links_unverified: unknown[];
  reason: string | null;
};
const API = "https://api.dexscreener.com";
const MIN_LIQUIDITY_USD = 100000;
const pause = (ms: number) => new Promise<void>(resolve => setTimeout(resolve, ms));
const num = (v: unknown): number | null =>
  typeof v === "number" && Number.isFinite(v) && v >= 0 ? v : null;
const signed = (v: unknown): number | null =>
  typeof v === "number" && Number.isFinite(v) ? v : null;
const base58 = /^[1-9A-HJ-NP-Za-km-z]{32,44}$/;
const logScore = (value: number | null, reference: number) =>
  value === null ? 0 : Math.min(1, Math.log10(1 + value) / Math.log10(1 + reference));
async function pairsFor(mint: string): Promise<Pair[]> {
  const res = await fetch(API + "/token-pairs/v1/solana/" + mint, { headers: { accept: "application/json" } });
  if (!res.ok) throw new Error("HTTP " + res.status);
  const data: unknown = await res.json();
  if (!Array.isArray(data)) throw new Error("Expected a list of pairs");
  return data as Pair[];
}
function bestPool(mint: string, pairs: Pair[]): Pair | null {
  const filtered = pairs.filter(p => p.chainId === "solana" &&
    (p.baseToken?.address === mint || p.quoteToken?.address === mint));
  // Largest liquidity pool as representative; never sum overlapping pool volumes.
  return filtered.sort((a,b) => (num(b.liquidity?.usd) ?? 0) - (num(a.liquidity?.usd) ?? 0))[0] ?? null;
}
function classify(c: Candidate, p: Pair | null): Ranked {
  const liquidity = num(p?.liquidity?.usd);
  const volume = num(p?.volume?.h24);
  const buys = num(p?.txns?.h24?.buys);
  const sells = num(p?.txns?.h24?.sells);
  const transactions = buys !== null && sells !== null ? buys + sells : null;
  const priceChange = signed(p?.priceChange?.h24);
  const eligible = liquidity !== null && liquidity >= MIN_LIQUIDITY_USD && volume !== null;
  // Market activity score, *not* an investment return forecast. Boosting is not awarded points.
  const score = eligible ? Math.round(100 * (
    .45 * logScore(volume, 10000000) +
    .35 * logScore(liquidity, 5000000) +
    .20 * logScore(transactions, 10000)
  ) * 100) / 100 : 0;
  const token = p?.baseToken?.address === c.mint ? p.baseToken : p?.quoteToken?.address === c.mint ? p.quoteToken : null;
  const telegramClaimed = (c.links_unverified ?? []).some(link =>
    typeof link === "object" && link !== null && JSON.stringify(link).toLowerCase().includes("t.me/"));
  return { rank: null, mint: c.mint, name: token?.name ?? null, symbol: token?.symbol ?? null,
    eligible, score, liquidity_usd: liquidity, volume_24h_usd: volume,
    transactions_24h: transactions, price_change_24h_percent: priceChange,
    pair_address: p?.pairAddress ?? null, dex: p?.dexId ?? null,
    boost_amount: num(c.boost_amount), total_boost_amount: num(c.total_boost_amount),
    telegram_claimed: telegramClaimed, telegram_verified: false,
    pair_url: p?.url ?? null, claimed_url: c.claimed_url ?? null,
    links_unverified: c.links_unverified ?? [], reason: !p ? "No matching Solana pool" :
      liquidity === null ? "Missing liquidity data" :
      liquidity < MIN_LIQUIDITY_USD ? "Below liquidity threshold" :
      volume === null ? "Missing volume data" : null };
}
async function main() {
  const input = Deno.args[0];
  if (!input) throw new Error("Supply a discovery JSON file path");
  const discovery = JSON.parse(await Deno.readTextFile(input)) as Discovery;
  if (discovery.schema !== "sbstats/discovery-v1" || !Array.isArray(discovery.candidates) ||
      !/^\d{4}-\d{2}-\d{2}$/.test(discovery.date_utc)) throw new Error("Invalid discovery JSON");
  const seen = new Set<string>();
  const ranked: Ranked[] = [];
  const errors: {mint: string; error: string}[] = [];
  for (const c of discovery.candidates) {
    if (!base58.test(c.mint) || seen.has(c.mint)) continue;
    seen.add(c.mint);
    try {
      const pool = bestPool(c.mint, await pairsFor(c.mint));
      ranked.push(classify(c, pool));
    } catch (e) {
      errors.push({mint:c.mint, error:String(e)});
    }
    await pause(350);
  }
  ranked.sort((a,b) => Number(b.eligible) - Number(a.eligible) || b.score - a.score || a.mint.localeCompare(b.mint));
  let rank = 0;
  for (const r of ranked) if (r.eligible) r.rank = ++rank;
  const output = {
    schema:"sbstats/candidate-ranking-v1", date_utc:discovery.date_utc,
    collected_at_utc:new Date().toISOString(),
    source:API+"/token-pairs/v1/solana/{mint}", input,
    definition:"Paid-boost discovery, ranked by representative highest-liquidity pool; rolling 24h vendor metrics",
    min_liquidity_usd:MIN_LIQUIDITY_USD, scoring:"45% log volume + 35% log liquidity + 20% log trades; no boost or social score",
    caveats:["Not top Solana tokens overall","Not verified on-chain transfers","Telegram links are unverified claims",
      "Ranking excludes failed API calls; do not treat missing as zero","A price change is not included in the score"],
    top_ten:ranked.filter(r => r.eligible).slice(0,10).map(r => r.mint),
    candidates:ranked, errors
  };
  const destination = input.replace(/\/discovery\/([^/]+)\.json$/, "/rankings/$1.json");
  if (destination === input) throw new Error("Input path must end with /discovery/YYYY-MM-DD.json");
  await Deno.mkdir(destination.slice(0,destination.lastIndexOf("/")), {recursive:true});
  await Deno.writeTextFile(destination, JSON.stringify(output,null,2)+"\n");
  console.log("Ranked:",ranked.length,"eligible:",rank,"top ten:",output.top_ten.length,"errors:",errors.length);
  console.log("Wrote:",destination);
}
if (import.meta.main) main().catch(e => {console.error(e);Deno.exitCode=1;});
