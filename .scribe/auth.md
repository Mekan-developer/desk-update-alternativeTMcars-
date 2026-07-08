# Authenticating requests

To authenticate requests, include an **`Authorization`** header with the value **`"Bearer {YOUR_SANCTUM_TOKEN}"`**.

All authenticated endpoints are marked with a `requires authentication` badge in the documentation below.

Токен выдаётся эндпоинтом `POST /auth/verify` (Sanctum personal access token). Передавайте его в заголовке `Authorization: Bearer {token}`.
