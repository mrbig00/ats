#!/usr/bin/env bash
# Re-apply Grafana Helm release so provisioning picks up datasource URL changes, then restart.
set -euo pipefail

NS=observability
DIR="$(cd "$(dirname "$0")" && pwd)"

cd "$DIR"
echo "==> Helm upgrade Grafana..."
helmfile apply -l name=grafana

echo "==> Verify Tempo URL in ConfigMap (expect :3100)..."
kubectl get configmap grafana -n "$NS" -o jsonpath='{.data.datasources\.yaml}' | grep -A2 'name: Tempo' || true

echo "==> Recreate Grafana pod (avoids RWO volume deadlock on rolling update)..."
kubectl rollout restart deployment/grafana -n "$NS" 2>/dev/null || true
kubectl scale deployment/grafana -n "$NS" --replicas=0
kubectl wait --for=delete pod -l app.kubernetes.io/name=grafana -n "$NS" --timeout=120s 2>/dev/null \
  || kubectl delete pod -n "$NS" -l app.kubernetes.io/name=grafana --force --grace-period=0 2>/dev/null || true
kubectl scale deployment/grafana -n "$NS" --replicas=1
kubectl rollout status deployment/grafana -n "$NS" --timeout=5m

echo ""
echo "Done. In Grafana UI, Tempo URL should be:"
echo "  http://tempo.observability.svc.cluster.local:3100"
echo ""
echo "If it still shows :3200, hard-reset Grafana DB (dashboards are re-provisioned from git):"
echo "  kubectl scale deployment/grafana -n $NS --replicas=0"
echo "  kubectl delete pvc -n $NS -l app.kubernetes.io/name=grafana"
echo "  kubectl scale deployment/grafana -n $NS --replicas=1"
echo "  helmfile apply -l name=grafana"
