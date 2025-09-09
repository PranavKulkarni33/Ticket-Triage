<h1>Ticket Triage (Laravel + Vue)</h1>

<p>
    Functional Test for **BeMo**
  Tiny helpdesk app. Create tickets, run “classify” (queued job), and see a simple dashboard.
  Dark mode is built-in.
</p>

<p><strong>Stack:</strong> Laravel 11, SQLite, Vue 3, Vite, Axios, Chart.js</p>

<hr />

<h2>Quick Setup (≤ 10 steps)</h2>
<ol>
  <li>
    <p><strong>Clone & enter</strong></p>
    <pre><code>git clone &lt;https://github.com/PranavKulkarni33/Ticket-Triage.git&gt;
cd ticket-triage
</code></pre>
  </li>
  <li>
    <p><strong>Install PHP deps</strong></p>
    <pre><code>composer install
</code></pre>
  </li>
  <li>
    <p><strong>Install JS deps</strong></p>
    <pre><code>npm install
</code></pre>
  </li>
  <li>
    <p><strong>Make env & app key</strong></p>
    <pre><code>cp .env.example .env
php artisan key:generate
</code></pre>
  </li>
  <li>
    <p><strong>Create the SQLite file</strong></p>
    <pre><code>mkdir -p database
touch database/database.sqlite
</code></pre>
  </li>
  <li>
    <p><strong>Migrate + seed sample data</strong></p>
    <pre><code>php artisan migrate --seed
</code></pre>
  </li>
  <li>
    <p><strong>Run the web app</strong></p>
    <pre><code>php artisan serve
# opens http://127.0.0.1:8000
</code></pre>
  </li>
  <li>
    <p><strong>Run the queue worker</strong> (needed for “Classify”)</p>
    <pre><code>php artisan queue:work
</code></pre>
  </li>
  <li>
    <p><strong>Run the frontend (Vite)</strong></p>
    <pre><code>npm run dev
# visit http://127.0.0.1:8000/tickets
</code></pre>
  </li>
  <li>
    <p><strong>(Prod build)</strong></p>
    <pre><code>npm run build
</code></pre>
  </li>
</ol>

<p><strong>API endpoints:</strong></p>
<pre><code>GET    /api/tickets
POST   /api/tickets
GET    /api/tickets/{id}
PATCH  /api/tickets/{id}
POST   /api/tickets/{id}/classify
GET    /api/stats
</code></pre>

<p><strong>Bulk classify</strong> (dispatch many jobs at once):</p>
<pre><code>php artisan tickets:bulk-classify --status=open --limit=20
</code></pre>

<hr />

<h2>Assumptions &amp; Trade-offs</h2>
<ul>
  <li><strong>SQLite</strong> for zero setup. Easy to review. (Switch to MySQL/Postgres by changing <code>.env</code>.)</li>
  <li><strong>No auth</strong> to keep the demo small. All calls are same-origin.</li>
  <li><strong>Classify</strong> uses a <em>dummy classifier</em> by default (<code>OPENAI_CLASSIFY_ENABLED=false</code>) so it works without keys.</li>
  <li><strong>Rate limiting</strong>: the classify API is throttled; the bulk command paces jobs.</li>
  <li><strong>UI</strong> is simple: vanilla CSS + dark mode toggle. No heavy UI kit.</li>
  <li><strong>Validation</strong> is basic (subject/body on create; light checks on patch).</li>
</ul>

<h3>What I’d do with more time</h3>
<ul>
  <li>Hook up real OpenAI in <code>App\Services\TicketClassifier</code>.</li>
  <li>Add auth + roles (agent/viewer).</li>
  <li>Better filters/sorting, bulk actions.</li>
  <li>E2E tests (Pest + Playwright) and GitHub Actions CI.</li>
  <li>Dockerfile + Makefile for one-command spin-up.</li>
</ul>

<hr />

<h2>Notes for reviewers</h2>
<ul>
  <li><strong>No limit</strong> on commits or branch structure — use what you like.</li>
  <li>Frontend under <code>resources/js</code>; API in <code>routes/api.php</code> + controllers.</li>
  <li>Keep <code>php artisan queue:work</code> running to see classify results update.</li>
</ul>

<hr />

<h2>.env.example</h2>
<p>Copy this into a file named <code>.env.example</code> at the repo root.</p>

<pre><code># ---- App ----
APP_NAME=TicketTriage
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

# ---- Logs / Cache ----
LOG_CHANNEL=stack
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=database

# ---- DB (SQLite by default) ----
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

# ---- OpenAI (dummy OFF by default) ----
OPENAI_API_KEY=
OPENAI_CLASSIFY_ENABLED=false

# ---- Misc ----
# If you switch to MySQL/Postgres, comment the sqlite lines above
# and set DB_HOST/DB_PORT/DB_DATABASE/DB_USERNAME/DB_PASSWORD here.
</code></pre>
