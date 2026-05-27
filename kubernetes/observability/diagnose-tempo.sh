#!/usr/bin/env bash
set -euo pipefail

NS=observability

echo "=== Tempo pod ==="
kubectl get pods -n "$NS" -l app.kubernetes.io/name=tempo -o wide

echo ""
echo "=== Tempo service ports ==="
kubectl get svc tempo -n "$NS" -o yaml | grep -A30 '^spec:'

echo ""
echo "=== Endpoints ==="
kubectl get endpoints tempo -n "$NS" 2>/dev/null || true

echo ""
echo "=== Query API (port 3100 on this chart) ==="
kubectl run tempo-test --rm -i --restart=Never -n "$NS" --image=curlimages/curl -- \
  curl -sS -m 10 "http://tempo.${NS}.svc.cluster.local:3100/ready" && echo ""

echo ""
echo "Grafana datasource URL should be: http://tempo.${NS}.svc.cluster.local:3100"
