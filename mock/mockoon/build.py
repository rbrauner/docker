#!/usr/bin/env python3
"""Build a Mockoon environment file from per-route fragments.

Mockoon loads ONE environment file per API, so we keep one small JSON fragment
per route under ./data/routes/ and compile them into ./data/environment.json.

Run after editing any fragment:

    python3 mock/mockoon/build.py

Fragment schema (compact; the builder fills in Mockoon's boilerplate):
    {
      "method": "get",                       # http method
      "endpoint": "test/hello-world",        # path, ":id" for params
      "responseMode": null | "SEQUENTIAL" | "RANDOM",
      "responses": [
        {
          "status": 200,                     # default 200
          "headers": { "Content-Type": "text/plain",
                       "Set-Cookie": ["a=1", "b=2"] },   # value may be a list
          "body": "..." | ["line", "line"],  # list is joined with newlines
          "json": { ... },                   # convenience: serialized to body
          "file": "/data/files/x.pdf",       # serve a file as the body
          "latency": 0,                      # ms
          "default": true,                   # fallback response (default true)
          "rules": [ { "target": "query", "modifier": "type",
                       "value": "premium", "operator": "equals" } ]
        }
      ]
    }
"""
import json, os, uuid, glob

HERE = os.path.dirname(os.path.abspath(__file__))
ROUTES_DIR = os.path.join(HERE, "data", "routes")
OUT = os.path.join(HERE, "data", "environment.json")
NS = uuid.UUID("00000000-0000-0000-0000-0000000000aa")

def uid(name):
    return str(uuid.uuid5(NS, name))

def build_headers(h):
    out = []
    for key, val in (h or {}).items():
        for v in (val if isinstance(val, list) else [val]):
            out.append({"key": key, "value": v})
    return out

def build_response(route_key, idx, r):
    body = r.get("body", "")
    if isinstance(body, list):
        body = "\n".join(body)
    headers = dict(r.get("headers", {}))
    if "json" in r:
        body = json.dumps(r["json"])
        headers.setdefault("Content-Type", "application/json")
    is_file = "file" in r
    return {
        "uuid": uid(f"{route_key}-resp-{idx}"),
        "body": body,
        "latency": r.get("latency", 0),
        "statusCode": r.get("status", 200),
        "label": r.get("label", ""),
        "headers": build_headers(headers),
        "bodyType": "FILE" if is_file else "INLINE",
        "filePath": r.get("file", ""),
        "databucketID": "",
        "sendFileAsBody": bool(is_file),
        "rules": [
            {"target": rl["target"], "modifier": rl.get("modifier", ""),
             "value": rl.get("value", ""), "invert": rl.get("invert", False),
             "operator": rl.get("operator", "equals")}
            for rl in r.get("rules", [])
        ],
        "rulesOperator": r.get("rulesOperator", "OR"),
        "disableTemplating": r.get("disableTemplating", False),
        "fallbackTo404": False,
        "default": r.get("default", True),
        "crudKey": "id",
        "callbacks": [],
    }

def build_route(frag):
    key = frag["method"] + " " + frag["endpoint"]
    return {
        "uuid": uid("route-" + key),
        "type": "http",
        "documentation": frag.get("documentation", ""),
        "method": frag["method"],
        "endpoint": frag["endpoint"],
        "responses": [build_response(key, i, r)
                      for i, r in enumerate(frag["responses"])],
        "responseMode": frag.get("responseMode"),
    }

def main():
    files = sorted(glob.glob(os.path.join(ROUTES_DIR, "*.json")))
    routes = [build_route(json.load(open(f))) for f in files]
    env = {
        "uuid": uid("env"),
        "lastMigration": 33,
        "name": "mockoon test",
        "endpointPrefix": "",
        "latency": 0,
        "port": 3000,
        "hostname": "",
        "folders": [],
        "routes": routes,
        "rootChildren": [{"type": "route", "uuid": r["uuid"]} for r in routes],
        "proxyMode": False,
        "proxyHost": "",
        "proxyRemovePrefix": False,
        "tlsOptions": {"enabled": False, "type": "CERT", "pfxPath": "",
                       "certPath": "", "keyPath": "", "caPath": "", "passphrase": ""},
        "cors": True,
        "headers": [],
        "proxyReqHeaders": [{"key": "", "value": ""}],
        "proxyResHeaders": [{"key": "", "value": ""}],
        "data": [],
        "callbacks": [],
    }
    with open(OUT, "w") as f:
        json.dump(env, f, indent=2)
        f.write("\n")
    print(f"built {OUT} from {len(routes)} route fragment(s):")
    for r in routes:
        print(f"  {r['method']:5} /{r['endpoint']}")

if __name__ == "__main__":
    main()
