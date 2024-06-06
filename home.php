<?php
  session_start();

  include("php/config.php");
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Expense Tracker</title>
    <link rel="stylesheet" href="style/homestyle.css" />
  </head>
  <body>
  <nav>
        <div class="logo">
            <img src="images/wallet.png" alt="Logo" />
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
        </div>
        <div class="right-links">
            <?php
            $id = $_SESSION['id'];
            $query = mysqli_query($con, "SELECT * FROM users WHERE Id=$id");

            while ($result = mysqli_fetch_assoc($query)) {
                $res_id = $result['Id'];
            }
            echo "<a href='edit.php?Id=$res_id' class='user-logo-link'><img src='images/userlogo.png' alt='user logo' class='user-logo'/></a>";

            ?>  

            <a href="php/logout.php"><button class="btn">Log Out</button></a>
        </div>
    </nav>
    
    <h1>Expense Tracker</h1>

    <main>
      <header>
        <div>
          <h5>Total Balance</h5>
          <span id="balance">0.00</span>
        </div>
        <div>
          <h5>Income</h5>
          <span id="income">$0.00</span>
        </div>
        <div>
          <h5>Expense</h5>
          <span id="expense">$0.00</span>
        </div>
      </header>

      <section>
        <h3>Transactions</h3>
        <ul id="transactionList"></ul>
        <div id="status"></div>
      </section>

      <section>
        <h3>Add Transaction</h3>

        <form id="transactionForm">
          <div>
            <label for="type">
              <input type="checkbox" name="type" id="type" />
              <div class="option">
                <span>Expense</span>
                <span>Income</span>
              </div>
            </label>
          </div>
          <div>
            <label for="name">Name</label>
            <input type="text" name="name" required />
          </div>
          <div>
            <label for="amount">Amount</label>
            <input
              type="number"
              name="amount"
              value="0"
              min="0.01"
              step="0.01"
              required
            />
          </div>
          <div>
            <label for="date">Date</label>
            <input type="date" name="date" required />
          </div>
          <button type="submit">Submit</button>
        </form>
      </section>
    </main>

    <script src="javascript/app.js"></script>
  </body>
</html>
