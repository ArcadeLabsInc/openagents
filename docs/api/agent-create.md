---
title: Create Agent
curl: curl blah blah!!!
responses:
  - 200: '{"successful response!!!"}'
  - 400: '{"asdfsadfsd!!!"}'
---

# Create agent

POST https://openagents.com/api/v1/agents

### Request parameters

* name (string, required): The name of the agent.
* description (string, required): A brief description of the agent's purpose.
* instructions (string, required): Detailed instructions on how the agent operates.

### Request example

```shell
curl https://openagents.com/api/v1/agents \
  -H "Authorization: Bearer $OPENAGENTS_API_KEY" \
  -H 'Content-Type: application/json' \
  -d '{
    "name": "Data Visualizer",
    "description": "Analyzes .csv files and creates data visualizations.",
    "instructions": "Upload a .csv file to begin.",
  }'
```

### Response parameters

* success: A boolean indicating whether the operation was successful.
* message: A human-readable message indicating the result of the operation.
* data: An object containing:
    * agent_id: The newly created agent's ID

### Response example

```shell
{
  "success": true,
  "message": "Agent created successfully.",
  "data": {
    "agent_id": 42
  }
}
```
