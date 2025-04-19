<?php
// Database connection settings
$servername = "localhost";
$username = "akash";
$password = "eckWDRkGezHJd7HH";
$dbname = "akash";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle the SQL query submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['sql_query'])) {
    $sql_query = $_POST['sql_query']; // Get the SQL query from the form

    // Clean the input: trim spaces and newlines
    $clean_query = trim($sql_query);
    $clean_query = str_replace("\r\n", " ", $clean_query);

    // Check if it's an UPDATE query and handle separately
    if (strpos(strtoupper($clean_query), 'UPDATE') !== false) {
        // Match a basic UPDATE query with WHERE id = something
        $pattern = '/UPDATE\s+([a-zA-Z0-9_]+)\s+SET\s+([a-zA-Z0-9_]+)\s*=\s*(\'[^\']+\')\s+WHERE\s+id\s*=\s*(\d+)/i';
        if (preg_match($pattern, $clean_query, $matches)) {
            $table = $matches[1];
            $column = $matches[2];
            $value = trim($matches[3], "'"); // remove quotes
            $id = (int)$matches[4];

            // Use prepared statement
            $stmt = $conn->prepare("UPDATE $table SET $column = ? WHERE id = ?");
            $stmt->bind_param("si", $value, $id);

            if ($stmt->execute()) {
                echo "<p>Record updated successfully!</p>";
            } else {
                echo "<p style='color:red;'>Error: " . htmlspecialchars($stmt->error) . "</p>";
            }
            $stmt->close();
        } else {
            echo "<p style='color:red;'>Invalid UPDATE query format.</p>";
        }
    } else {
        // For other queries (SELECT, SHOW, INSERT, etc.)
        $result = $conn->query($clean_query);

        if ($conn->error) {
            echo "<p style='color:red;'>Error in query: " . htmlspecialchars($conn->error) . "</p>";
        } else {
            // Detect if the query returns a result set
            $first_word = strtoupper(strtok($clean_query, " "));
            if (in_array($first_word, ['SELECT', 'SHOW', 'DESCRIBE', 'EXPLAIN'])) {
                if ($result->num_rows > 0) {
                    echo "<h3>Query Results:</h3>";
                    echo "<table border='1' cellpadding='5' cellspacing='0'><tr>";

                    // Output column headers
                    $fields = $result->fetch_fields();
                    foreach ($fields as $field) {
                        echo "<th>" . htmlspecialchars($field->name ?? '') . "</th>";
                    }
                    echo "</tr>";

                    // Output rows
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        foreach ($row as $value) {
                            echo "<td>" . htmlspecialchars($value ?? '') . "</td>";
                        }
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "No results found for the query.";
                }
            } else {
                // For INSERT, DELETE, etc.
                echo "<p>Query executed successfully!</p>";
            }
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Query Interface</title>
</head>
<body>
    <h2>Run SQL Query</h2>
    <form action="" method="post">
        <textarea name="sql_query" rows="5" cols="60" placeholder="Enter your SQL query here..."></textarea><br><br>
        <input type="submit" value="Run Query">
    </form>
</body>
</html>
