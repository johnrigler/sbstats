# Base DeFi workshop and $100 wallet experiments

Status: proposal, 2026-10-11. No capital committed or contracts deployed.

## Goal
Teach wallet operations and observable DeFi economics in a repeatable workshop for Grants/Milan, New Mexico, and ETHDenver. Begin with a funded-wallet demonstration, not profit claims. Participants must not be expected to buy a project token.

## Candidate demonstrations
1. **USDC lending**: show deposits, variable supply APY, accrued interest, withdrawal, lending/contract/stablecoin risks.
2. **ETH/USDC LP**: compare holding 50/50 with LP position. Track fees, pool price, range if concentrated liquidity, gas, rebalances and impermanent loss.
3. **Token inspection**: use the existing stats discovery tools to examine Base or Solana tokens, their official links, pool liquidity, holder concentration, verified contract metadata, recent swaps, and red flags. A $20 speculation example is a *loss-budget demonstration*, not a recommendation.
4. **Incentives**: separate ordinary transaction fee revenue from volatile reward tokens and distinguish nominal APR from net P/L.

## Model wallet (illustrative, not advice)
- $50 lending demonstration
- $30 LP demonstration
- $20 research/trading budget
- Additional gas reserve required; with a hard $100 total budget, reduce the allocations to leave transaction fees.
Use testnets or simulations first. Never collect attendees' seed phrases or private keys.

## Proposed dashboard
Browser-only vanilla JS, deployable on IPFS or GitHub Pages; no Node runtime required.
- Wallet address and chain ID (Base mainnet 8453)
- Starting cash, contributed/withdrawn funds, gas paid, current positions, token balances
- Lending supply APY and interest accrued; timestamps and API provenance
- LP deposited amounts, position value, fees collected/uncollected, benchmark HODL value
- Token socials and source links, contract address, audit/verification status if known
- Profit/loss = current withdrawable USD value + withdrawn USD - deposits USD - all fees, separating realized/unrealized amounts
- Mark stale prices and RPC/API failures instead of silently estimating

## Important rules
No guaranteed returns. Rates, yields, and gas costs must be live-checked at demo time, not copied from a past conversation. Explicitly show contract risk, wallet risk, market risk, and stablecoin depeg risk. Avoid custody, investment solicitation, or unvetted financial promises. Show participants how to revoke allowances.

## Implementation backlog
- [ ] Review existing sbstats ingestion/CLI architecture before choosing Python or JS; do not rewrite the Python workflow as Deno/TypeScript.
- [ ] Add Base chain support as an optional data source, separate from Solana.
- [ ] Add pool and token metadata with website/social links and clear source attribution.
- [ ] Implement hypothetical portfolio ledger and fee-aware P/L calculations.
- [ ] Add exportable CSV/JSON workshop snapshots for month-over-month comparisons.
- [ ] Build static browser dashboard and simple documentation with a sample wallet.
- [ ] Only then investigate optional read-only wallet integration.

Cross-references: johnrigler/chisel for identity/ledger attestations; johnrigler/lantern is a separate optional demonstration, not a prerequisite.
