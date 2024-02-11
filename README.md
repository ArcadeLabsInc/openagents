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

## Tech Stack
- Laravel
- HTMX
- Tailwind CSS
- MySQL

## Video series
We've chronicled most of the development of this platform over multiple months and 70+ videos on X.

See [episode one](https://twitter.com/OpenAgentsInc/status/1721942435125715086) or the [full episode list](https://github.com/OpenAgentsInc/openagents/wiki/Video-Series).
