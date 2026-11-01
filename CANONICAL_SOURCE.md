# Canonical source

The deployable Laravel application is the repository root: `app/`, `config/`,
`database/`, `resources/`, `routes/`, `public/`, and `tests/`.

`manual-upload/` and `website/` are retained archive/import trees. They are not
deployment sources and must not receive isolated feature changes. Deployment
packages are produced from the repository root by `deploy.sh`.
