<?php
$init_sql = "
      CREATE TABLE IF NOT EXISTS columns (
          id INT AUTO_INCREMENT PRIMARY KEY,
          name VARCHAR(100) NOT NULL,
          `order` INT NOT NULL
      );

  CREATE TABLE IF NOT EXISTS tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    column_id INT NOT NULL,
    `order` INT NOT NULL,
    user_id INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (column_id) REFERENCES columns(id),
          FOREIGN KEY (user_id) REFERENCES users(id)

);

    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL
        ADD COLUMN password VARCHAR(255) NOT NULL,
        ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ADD COLUMN loginip VARCHAR(255) NOT NULL,
        ADD COLUMN lastlogin TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    );

CREATE TABLE tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

CREATE TABLE task_tags (
    task_id INT NOT NULL,
    tag_id INT NOT NULL,
    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE,
    PRIMARY KEY (task_id, tag_id)
);
    ";
    // INSERT INTO columns (id, name, `order`) VALUES
    // (1, 'Backlog', 1),
    // (2, 'Selected for Development', 2),
    // (3, 'In Progress', 3),
    // (4, 'Done', 4)


// try {
//     $pdo->exec($init_sql);
// } catch(PDOException $e) {
//     echo "Table creation failed: " . $e->getMessage();
// }
/*
// Optionally, you can hash and set passwords for existing users
$users = $pdo->query("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC);
foreach ($users as $user) {
    $hashedPassword = password_hash('password', PASSWORD_DEFAULT);
    $pdo->prepare("UPDATE users SET password = ? WHERE id = ?")->execute([$hashedPassword, $user['id']]);
}
// Update existing columns and add new one
try {
  // First, drop the foreign key constraint from tasks table
  $pdo->exec("ALTER TABLE tasks DROP FOREIGN KEY tasks_ibfk_1");

  // Drop existing columns table
  $pdo->exec("DROP TABLE IF EXISTS columns");

  // Recreate columns table
  $pdo->exec("
      CREATE TABLE columns (
          id INT AUTO_INCREMENT PRIMARY KEY,
          name VARCHAR(100) NOT NULL,
          `order` INT NOT NULL
      )
  ");

  // Recreate tasks table
  $pdo->exec("

  CREATE TABLE IF NOT EXISTS tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    column_id INT NOT NULL,
    `order` INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (column_id) REFERENCES columns(id)
)
  ");

    // Create users table
    $pdo->exec("
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL
    )
");

  // Insert new column structure
  $pdo->exec("
      INSERT INTO columns (id, name, `order`) VALUES
      (1, 'Backlog', 1),
      (2, 'Selected for Development', 2),
      (3, 'In Progress', 3),
      (4, 'Done', 4)
  ");

  // Recreate foreign key constraint
  $pdo->exec("
      ALTER TABLE tasks
      ADD CONSTRAINT tasks_ibfk_1
      FOREIGN KEY (column_id) REFERENCES columns(id)
  ");

  // Update any existing tasks that were in the "To Do" column (id=1)
  // to be in "Selected for Development" (now id=2)
  // $pdo->exec("
  //     UPDATE tasks
  //     SET column_id = 2
  //     WHERE column_id = 1
  // ");



  // Add user_id column to tasks table
  $pdo->exec("
      ALTER TABLE tasks
      ADD COLUMN user_id INT DEFAULT NULL,
      ADD CONSTRAINT fk_user
      FOREIGN KEY (user_id) REFERENCES users(id)
  ");

} catch(PDOException $e) {
  echo "Table update failed: " . $e->getMessage();
}*/
?>