# ETHDenver metrics and Zorkmid distribution research

Planning only. [Event overview](https://github.com/johnrigler/EthDenver/blob/main/docs/ethdenver-2027-showcase.md).

Measure whether technical visitors actually enter the Chisel/Lantern world after receiving a physical card; do not mistake boosted memecoin trading for product adoption.

Suggested privacy-preserving event schema:
```json
{"event":"claim_complete","time":"2027-02-01T00:00:00Z","campaign":"ethdenver-demo","card_id":"random-public-reference","chain":"base","wallet_hash":"optional-pseudonymous-id","tx_hash":"optional","source":"human-opt-in"}
```
This is a schema example, not a factual event or confirmed event date. Avoid storing sensitive wallet/private information or long-lived cross-site identifiers without need.

Funnels: card distributed (manual aggregate), QR visits, Lantern starts, artifact views, opt-in claims, distinct self-directed transfers, repeat visits. Deduplicate by session/tx ID; separate testnet from mainnet and human activity from agents. Airdropped holders are not automatically engaged users. Price/volume/liquidity are secondary optional market observations.

Research tracks: Base vs Polygon gas benchmarking; actual claim costs per recipient; transfer/etch event read reliability; on-chain holder changes; adverse cases such as bots, shared addresses, fake activity and unsolicited token spam. Reuse existing Python read-only Solana discovery without changing its input or claiming it tracks Base.

Do not trade against your own controlled wallets to manufacture adoption/volume; no automatic mainnet minting or unsolicited transfers.
