# OpenAgents

An open platform for AI agents, built in public from scratch. (wip)

See the [wiki](https://github.com/OpenAgentsInc/openagents/wiki) for more. Now building our [MVP Spec](https://github.com/OpenAgentsInc/openagents/wiki/MVP-Spec).

![builder1](https://github.com/OpenAgentsInc/openagents/assets/14167547/2114cfed-5731-4d50-9a11-1f58de3b41e9)

## How it works

- A user creates an Agent from Nodes.
- Nodes are defined by community developers. They could be:
  - API endpoints
  - External WASM plugins
  - Conditional logic
  - Data parsing
  - Or most anything compatible with flow-based programming
- Nodes may have an associated fee which is paid to the node creator when the node is used in a workflow
- Agents can be used in our UI and via API
- Users can comment/rate/share Nodes
- Leaderboards show what's popular

## Tech Stack
- Laravel
- HTMX
- Tailwind CSS
- MySQL

## Video series
We've chronicled most of the development of this platform over multiple months and 70+ videos on X.

See [episode one](https://twitter.com/OpenAgentsInc/status/1721942435125715086) or the [full episode list](https://github.com/OpenAgentsInc/openagents/wiki/Video-Series).

1. [Intro](https://twitter.com/OpenAgentsInc/status/1721942435125715086)
2. [Choosing a Tech Stack](https://twitter.com/OpenAgentsInc/status/1721966796515754266)
3. [Hello Laravel](https://twitter.com/OpenAgentsInc/status/1721979219763155232)
4. [Deploying a Landing Page](https://twitter.com/OpenAgentsInc/status/1722068606714835283)
5. [Agent First Principles](https://twitter.com/OpenAgentsInc/status/1722274309727752427)
6. [Implementing Agent Data Models via TDD](https://twitter.com/OpenAgentsInc/status/1722287956419871177)
7. [First Feature Tests](https://twitter.com/OpenAgentsInc/status/1722313899771347362)
8. [Agent UX Design](https://twitter.com/OpenAgentsInc/status/1722742595409830389)
9. [Building the UI](https://twitter.com/OpenAgentsInc/status/1723164712957862115)
10. [Connecting to Vectara](https://twitter.com/OpenAgentsInc/status/1723203092647137636)
11. [Chatting with a PDF](https://twitter.com/OpenAgentsInc/status/1723525820357005661)
12. [RAG Planning](https://twitter.com/OpenAgentsInc/status/1723888973213286760)
13. [RAG First Principles](https://twitter.com/OpenAgentsInc/status/1724432749275095365)
14. [Embeddings 101](https://twitter.com/OpenAgentsInc/status/1724509783086989333)
15. [Similarity Search](https://twitter.com/OpenAgentsInc/status/1724568957598708192)
16. [PDF to Embeddings](https://twitter.com/OpenAgentsInc/status/1724801372602950026)
17. [Connecting the UI](https://twitter.com/OpenAgentsInc/status/1725197866409267544)
18. [Connecting the UI, Part 2](https://twitter.com/OpenAgentsInc/status/1725246583623590158)
19. [Chat with PDF, OpenAgents Edition](https://twitter.com/OpenAgentsInc/status/1725349984952827929)
20. [Planning a GitHub Agent](https://twitter.com/OpenAgentsInc/status/1725597044981617119)
21. [Hello Faerie](https://twitter.com/OpenAgentsInc/status/1725910351563165748)
22. [Conversing with Faerie](https://twitter.com/OpenAgentsInc/status/1725928497367908432)
23. [Embedding our Codebase](https://twitter.com/OpenAgentsInc/status/1725948809593638971)
24. [Faerie Makes a Plan](https://twitter.com/OpenAgentsInc/status/1725969687102534110)
25. [Faerie Writes Code](https://twitter.com/OpenAgentsInc/status/1725977712043372666)
26. [Faerie Commits Code](https://twitter.com/OpenAgentsInc/status/1727018763915247784)
27. [Smarter Pull Requests](https://twitter.com/OpenAgentsInc/status/1727424427825193041)
28. [Creating New Files](https://twitter.com/OpenAgentsInc/status/1727433378063135085)
29. [Automating Tests](https://twitter.com/OpenAgentsInc/status/1728590361805672788)
30. [Faerie Debugs Failing Tests](https://twitter.com/OpenAgentsInc/status/1728614813675274300)
31. [Faerie as Daemon](https://twitter.com/OpenAgentsInc/status/1730253928896291251)
32. [Toward Semi-Automation](https://twitter.com/OpenAgentsInc/status/1731086330694651924)
33. [Agent Inspectability Planning](https://twitter.com/OpenAgentsInc/status/1731156734335398303)
34. [Agent Inspection UX Design](https://twitter.com/OpenAgentsInc/status/1731733390641050106)
35. [Agent Inspection UI](https://twitter.com/OpenAgentsInc/status/1734044762255036737)
36. [Agent Modules 101](https://twitter.com/OpenAgentsInc/status/1738000844476371445)
37. [Flow of Funds](https://twitter.com/OpenAgentsInc/status/1738221896234373387)
38. [Agent Node Graphs](https://twitter.com/OpenAgentsInc/status/1741887869055119630)
39. [Component-Driven Development](https://twitter.com/OpenAgentsInc/status/1742232060821934216)
40. [Agent Brain Design](https://twitter.com/OpenAgentsInc/status/1742346953210388881)
41. [Hello Concierge](https://twitter.com/OpenAgentsInc/status/1742609184875544613)
42. [Agent Bitcoin Balance](https://twitter.com/OpenAgentsInc/status/1742952006166225330)
43. [EpsteinGPT Postmortem](https://twitter.com/OpenAgentsInc/status/1742970915061731424)
44. [Sleuth Agent Planning](https://twitter.com/OpenAgentsInc/status/1742992606785622272)
45. [Agent Builder & Chat UI](https://twitter.com/OpenAgentsInc/status/1744471277207773191)
46. [Hello EpsteinSleuth](https://twitter.com/OpenAgentsInc/status/1745521898824356193)
47. [Reviewing the GPT "Store"](https://twitter.com/OpenAgentsInc/status/1745545948908962228)
48. [Brainstorming a Plugin System](https://twitter.com/OpenAgentsInc/status/1745918872866173125)
49. [Plugin Registry Setup](https://twitter.com/OpenAgentsInc/status/1746989980562464915)
50. [Exploring HTMX in Laravel](https://twitter.com/OpenAgentsInc/status/1747325914650710363)
51. [HTMX Bitcoin Price Ticker](https://twitter.com/OpenAgentsInc/status/1747366650075025671)
52. [HTMX Server Sent Events](https://twitter.com/OpenAgentsInc/status/1747430710212702706)
53. [Loading WASM Plugins](https://twitter.com/OpenAgentsInc/status/1747791884414599350)
54. [Uploading a Plugin](https://twitter.com/OpenAgentsInc/status/1747994309549318228)
55. [Plugin Registry UI](https://twitter.com/OpenAgentsInc/status/1748445660146216995)
56. [Deleting JavaScript](https://twitter.com/OpenAgentsInc/status/1748536252733739412)
57. [Markdown Blog](https://twitter.com/OpenAgentsInc/status/1748909829500842046)
58. [Agent Uses Plugin](https://twitter.com/OpenAgentsInc/status/1749490769151287318)
59. [Agent Node Graphs, Litegraph Edition](https://twitter.com/OpenAgentsInc/status/1749850948296380668)
60. [Simpler Node Graph](https://twitter.com/OpenAgentsInc/status/1749990397714055567)
61. [Task Runner UI](https://twitter.com/OpenAgentsInc/status/1750252532348211598)
62. [Exploring L402](https://twitter.com/OpenAgentsInc/status/1750729304504213964)
63. [Agent Pays L402 Endpoint](https://twitter.com/OpenAgentsInc/status/1751700732963672411)
64. [Lightning Withdrawals](https://twitter.com/OpenAgentsInc/status/1752049402359754789)
65. [Exploring Code Llama 70B](https://twitter.com/OpenAgentsInc/status/1752464706365755475)
66. [Nostr Plugin Registry](https://twitter.com/OpenAgentsInc/status/1752537446003118431)
67. [Replacing ChatGPT](https://twitter.com/OpenAgentsInc/status/1752830191213150334)
68. [URL Extractor Plugin](https://twitter.com/OpenAgentsInc/status/1753092771206885743)
69. [URL Scraper Plugin](https://twitter.com/OpenAgentsInc/status/1753237589945905373)
70. [L402 Plugin Deployment](https://twitter.com/OpenAgentsInc/status/1753527990506721416)
71. [PHP Host Functions](https://twitter.com/OpenAgentsInc/status/1754643881172328732)
72. [LLM Inference Plugin](https://twitter.com/OpenAgentsInc/status/1755001183784075769)
73. [Agent Builder UI](https://twitter.com/OpenAgentsInc/status/1757111834857926954)
74. [Run Plugin Node](https://twitter.com/OpenAgentsInc/status/1757172886173827116)
75. [Run All Plugins](https://twitter.com/OpenAgentsInc/status/1757507045022945510)
