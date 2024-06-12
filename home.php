<?php
session_start();
include ("php/config.php");

// Check if the user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: home.php");
    exit();
}

$userId = $_SESSION['id'];

$query = $con->prepare("SELECT Type, Name, Amount, Date FROM transactions WHERE UserId = ?");
$query->bind_param("i", $userId);
$query->execute();
$result = $query->get_result();

$transactions = [];
while ($row = $result->fetch_assoc()) {
    $transactions[] = $row;
}

$query->close();
$con->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/homestyle.css">
    <title>Expense Tracker</title>
    <style>
        h1 {
            text-align: center;
            font-size: 1.5rem;
        }

        h3,
        header,
        ul {
            margin-bottom: 0.5rem;
        }

        main {
            max-width: 1200px;
            margin: 1rem auto;
            background-color: #fff;
            padding: 1rem;
            box-shadow: 0 3px 5px rgba(0, 0, 0, 0.1);
            display: grid;
            gap: 10px;
        }

        header {
            background-color: var(--main-color);
            color: #fff;
            padding: 1rem;
            text-align: center;
            border-radius: 5px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            justify-items: center;
        }

        header div {
            padding: 5px;
        }

        header h5 {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        form {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        form input:not(#type),
        form button {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 5px;
            height: 42px;
            font-family: "Poppins", sans-serif;
            font-size: 1rem;
        }

        form button {
            background-color: var(--main-color);
            color: #fff;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            box-shadow: 0 3px 5px rgba(0, 0, 0, 0.1);
        }

        form label {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        form div:nth-child(-n + 2) {
            flex-basis: 100%;
        }

        form div:nth-child(n + 3) {
            flex-basis: calc(50% - 5px);
        }

        input#type {
            appearance: none;
            position: absolute;
        }

        .option {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
            background: #eee;
            border-radius: 5px;
            position: relative;
        }

        .option span {
            width: 50%;
            text-align: center;
            cursor: pointer;
            z-index: 2;
        }

        .option::before {
            content: "";
            position: absolute;
            top: 5px;
            left: 0;
            background-color: #fff;
            height: calc(100% - 10px);
            width: calc(50% - 10px);
            transform: translateX(5px);
            border-radius: inherit;
            box-shadow: 0 2px 2px rgba(0, 0, 0, 0.1);
            transition: all 200ms;
        }

        input#type:checked~.option::before {
            left: 50%;
        }

        ul {
            list-style-type: none;
        }

        ul li {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 5px 10px;
            position: relative;
        }

        ul li:hover {
            background: rgba(0, 0, 0, 0.1);
        }

        .name {
            flex: 1;
        }

        .name h4 {
            font-size: 1rem;
            font-weight: 600;
            text-transform: capitalize;
        }

        .name p {
            font-size: 0.8rem;
            color: #555;
        }

        .amount {
            font-weight: 600;
        }

        .amount.income {
            color: yellowgreen;
        }

        .amount.expense {
            color: indianred;
        }

        .action {
            position: absolute;
            top: 0;
            right: 0;
            background-color: #f00;
            color: #fff;
            height: 100%;
            width: 50px;
            display: grid;
            place-items: center;
            transform: scaleX(0);
            transform-origin: right;
            transition: all 300ms;
        }

        ul li:hover .action {
            transform: scaleX(1);
        }

        .action svg {
            width: 36px;
            height: 36px;
            cursor: pointer;
        }

        #status {
            text-align: center;
            margin-bottom: 0.5rem;
        }
    </style>
</head>

<body>
    <nav>
        <div class="logo">
            <img src="images/wallet.png" alt="Logo" />
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
        </div>
        <div class="right-links">
            <a href="edit.php"><img src="images/userlogo.png" alt="user logo" class="user-logo"
                    style="height: 30px; margin-top: 15px" /></a>
            <a href="php/logout.php"><button class="btn">Log Out</button></a>
        </div>
    </nav>
    <h1>Expense Tracker</h1>

    <main>
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
                    <input type="number" name="amount" value="0" min="0.01" step="0.01" required />
                </div>
                <div>
                    <label for="date">Date</label>
                    <input type="date" name="date" required />
                </div>
                <button type="submit">Submit</button>
            </form>
        </section>
        <header>
            <div>
                <h5>Total Balance</h5>
                <span id="balance">$0.00</span>
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

    </main>


    <script>
        const transactions = JSON.parse(localStorage.getItem("transactions")) || [];

        const formatter = new Intl.NumberFormat("en-US", {
            style: "currency",
            currency: "USD",
            signDisplay: "always",
        });

        const list = document.getElementById("transactionList");
        const form = document.getElementById("transactionForm");
        const status = document.getElementById("status");
        const balance = document.getElementById("balance");
        const income = document.getElementById("income");
        const expense = document.getElementById("expense");

        form.addEventListener("submit", addTransaction);

        function updateTotal() {
            const incomeTotal = transactions
                .filter((trx) => trx.type === "income")
                .reduce((total, trx) => total + trx.amount, 0);

            const expenseTotal = transactions
                .filter((trx) => trx.type === "expense")
                .reduce((total, trx) => total + trx.amount, 0);

            const balanceTotal = incomeTotal - expenseTotal;

            balance.textContent = formatter.format(balanceTotal).substring(1);
            income.textContent = formatter.format(incomeTotal);
            expense.textContent = formatter.format(expenseTotal * -1);
        }

        function renderList() {
            list.innerHTML = "";

            status.textContent = "";
            if (transactions.length === 0) {
                status.textContent = "No transactions.";
                return;
            }

            transactions.forEach(({ id, name, amount, date, type }) => {
                const sign = "income" === type ? 1 : -1;

                const li = document.createElement("li");

                li.innerHTML = `
      <div class="name">
        <h4>${name}</h4>
        <p>${new Date(date).toLocaleDateString()}</p>
      </div>

      <div class="amount ${type}">
        <span>${formatter.format(amount * sign)}</span>
      </div>
    
      <div class="action">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" onclick="deleteTransaction(${id})">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
      </div>
    `;

                list.appendChild(li);
            });
        }

        renderList();
        updateTotal();

        function deleteTransaction(id) {
            const index = transactions.findIndex((trx) => trx.id === id);
            transactions.splice(index, 1);

            updateTotal();
            saveTransactions();
            renderList();
        }

        function addTransaction(e) {
            e.preventDefault();

            const formData = new FormData(this);

            transactions.push({
                id: transactions.length + 1,
                name: formData.get("name"),
                amount: parseFloat(formData.get("amount")),
                date: new Date(formData.get("date")),
                type: "on" === formData.get("type") ? "income" : "expense",
            });

            this.reset();

            updateTotal();
            saveTransactions();
            renderList();
        }

        function saveTransactions() {
            transactions.sort((a, b) => new Date(b.date) - new Date(a.date));

            localStorage.setItem("transactions", JSON.stringify(transactions));
        }

    </script>
</body>

</html>