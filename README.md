# Databricks Connect for Laravel

A lightweight Laravel package that allows you to connect to Databricks using ODBC and perform query operations such as `fetch`, `fetchOne`, `paginate`, and even bulk `insertOrUpdate` into your database.

---

## 📦 Installation

Install via Composer:

```bash
composer require ankitfromindia/databricks-connect


⸻

⚙️ Configuration

Publish the configuration file (optional if you want to customize):

php artisan vendor:publish --tag=databricks-config

Set up your .env and config/databricks.php like so:

return [
    'default' => 'your_connection_name',

    'connections' => [
        'your_connection_name' => [
            'driver' => 'Databricks ODBC Driver',
            'host' => 'dbc-xxxxxxxx-xxxx.cloud.databricks.com',
            'path' => '/sql/1.0/warehouses/your-warehouse-id',
            'token' => env('DATABRICKS_TOKEN'),
            'charset' => 'UTF-8',
            'aws_key' => '',
            'aws_secret' => '',
        ],
    ],
];


⸻

🚀 Usage

1. Connect to Databricks

use Ankitfromindia\DatabricksConnect\Databricks;

$dbx = Databricks::connect(); // Uses default connection

Or use a specific connection:

$dbx = Databricks::connect('your_connection_name');


⸻

2. Fetch All Rows

$data = $dbx->select("SELECT * FROM users")->fetch();

3. Fetch One Row

$user = $dbx->select("SELECT * FROM users WHERE id = 1")->fetchOne();

4. Stream Data with Cursor

foreach ($dbx->select("SELECT * FROM large_table")->fetchCursor() as $row) {
    // Process row
}


⸻

5. Pagination

$result = $dbx->select("SELECT * FROM users")->paginate(50, 100);
// returns ['limit' => 50, 'offset' => 100, 'data' => [...]]


⸻

6. Count Rows

$count = $dbx->select("SELECT * FROM users")->count();


⸻

7. Insert or Update

$data = [
    ['id' => 1, 'name' => 'Ankit'],
    ['id' => 2, 'name' => 'Vishwakarma']
];

$dbx->insertOrUpdate($data, 'users');


⸻

8. Fetch and Insert Large Datasets

$dbx->select("SELECT * FROM external_source")
    ->fetchAndInsertInto('local_table', null, 2000);


⸻

🧪 Advanced Options

Apply Limit/Offset Directly

$dbx->select("SELECT * FROM users")
    ->limit(100)
    ->offset(200)
    ->fetch();


⸻

🛠 Requirements
	•	PHP 8.0+
	•	Laravel 9+
	•	ODBC installed with Databricks ODBC Driver

⸻

📝 License

MIT

⸻

👤 Author

Maintained by @ankitfromindia

---

Let me know if you also want to publish this to Packagist, or need a `composer.json`, `config/databricks.php`, or test setup scaffolding.