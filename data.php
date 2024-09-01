<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #fd00f1;
            padding: 1em;
            text-align: center;
        }
        nav ul {
            list-style: none;
            padding: 0;
        }
        nav ul li {
            display: inline;
            margin: 0 10px;
        }
        nav ul li a {
            text-decoration: none;
            color: #000000;
            font-size: 1.2em;
            font-weight: bold;
        }
        nav ul li a:hover {
            text-decoration: underline;
        }
        main {
            padding: 2em;
        }
        h1 {
            text-align: center;
            color: #000000;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 2em 0;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .delete-btn {
            background-color: #ff4d4d;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }
        .delete-btn:hover {
            background-color: #cc0000;
        }
    </style>
    <script>
        //refreshen van pagina
        function refreshPage() {
            setTimeout(function() {
                location.reload();
            }, 5000); 
        }

        window.onload = refreshPage;
    </script>
</head>
<body>
    <header>
        <nav role="navigation">
            <ul>
                <li><a href="index.html">Home</a></li>
                <li><a href="about.html">About</a></li>
                <li><a href="data.php">Data</a></li>
                <li><a href="graphic.php">Graph</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h1>Data from Messages Table</h1>
        <?php
        // Database connection settings
        $host = 'localhost';
        $db   = 'messages_db';
        $user = 'viewer'; 
        $pass = 'Liang';  

        // Set up the DSN (Data Source Name)
        $dsn = "pgsql:host=$host;dbname=$db";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            // maken van PDO instance (connect database)
            $pdo = new PDO($dsn, $user, $pass, $options);

            // Verwijderen van bericht
            if (isset($_POST['delete_id'])) {
                $delete_id = (int)$_POST['delete_id'];
                $delete_sql = 'DELETE FROM public.messages WHERE id = :id';
                $delete_stmt = $pdo->prepare($delete_sql);
                $delete_stmt->execute(['id' => $delete_id]);
                echo '<p>Record deleted successfully.</p>';
            }

            // Data van berichten
            $sql = 'SELECT id, message, created_at FROM public.messages ORDER BY created_at DESC';
            $stmt = $pdo->query($sql);
            
            // Is er data aanwezig?
            if ($stmt->rowCount() > 0) {
                // Output data in een table
                echo '<table>';
                echo '<tr><th>ID</th><th>Message</th><th>Created At</th><th>Action</th></tr>';
                while ($row = $stmt->fetch()) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($row['id']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['message']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['created_at']) . '</td>';
                    echo '<td>';
                    echo '<form method="post" style="display:inline;">
                            <input type="hidden" name="delete_id" value="' . htmlspecialchars($row['id']) . '">
                            <button type="submit" class="delete-btn">Delete</button>
                          </form>';
                    echo '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            } else {
                echo '<p>No data available.</p>';
            }
        } catch (PDOException $e) {
            // Error bericht
            echo '<p>Error: ' . $e->getMessage() . '</p>';
        }
        ?>
    </main>
</body>
</html>
