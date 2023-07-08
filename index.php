<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Expenses Tracker</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <style>
    .sorting:after {
      content: "";
      display: inline-block;
      margin-left: 0.5em;
      vertical-align: middle;
      width: 0;
      height: 0;
      border-top: 4px solid;
      border-right: 4px solid transparent;
      border-left: 4px solid transparent;
    }

    .ascending:after {
      transform: rotate(180deg);
    }

    .descending:after {
      transform: rotate(0deg);
    }
  </style>
</head>

<body>
  <div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Expenses Tracker</h1>

    <!-- Add Expense Form -->
    <div class="mb-4">
      <h2 class="text-lg font-semibold mb-2">Add Expense</h2>
      <form id="addExpenseForm" class="flex items-center" method="POST" action="expenses_api.php">
        <input class="mr-2 px-2 py-1 border rounded" type="text" name="name" placeholder="Expense Name" required>
        <input class="mr-2 px-2 py-1 border rounded" type="number" step="0.01" name="amount" placeholder="Amount" required>
        <button class="px-4 py-2 bg-blue-500 text-white font-semibold rounded" type="submit">Add</button>
      </form>
    </div>

    <!-- Expenses Table -->
    <div>
      <h2 class="text-lg font-semibold mb-2">Expenses List</h2>
      <table class="w-full">
        <thead>
          <tr>
            <th class="py-2 px-4 bg-gray-200 font-semibold text-left">
              <button onclick="sortTable(0)" class="sorting">Expense Name</button>
            </th>
            <th class="py-2 px-4 bg-gray-200 font-semibold text-left">
              <button onclick="sortTable(1)" class="sorting">Amount</button>
            </th>
            <th class="py-2 px-4 bg-gray-200 font-semibold text-left">Actions</th>
          </tr>
        </thead>
        <tbody id="expensesList">
          <!-- Expenses will be dynamically inserted here -->
        </tbody>
      </table>
    </div>
  </div>

  <script>
    // Function to sort the expenses table based on the selected column
    function sortTable(column) {
      const expensesList = document.getElementById('expensesList');
      const rows = Array.from(expensesList.getElementsByTagName('tr'));

      rows.sort((a, b) => {
        const valueA = a.cells[column].textContent;
        const valueB = b.cells[column].textContent;

        if (column === 1) {
          return valueA - valueB;
        } else {
          return valueA.localeCompare(valueB);
        }
      });

      // Check if the column is already sorted in ascending order
      const isAscending = rows[0].cells[column].classList.contains('ascending');

      // Remove sorting classes from all column headers
      const headers = document.getElementsByTagName('th');
      for (let i = 0; i < headers.length; i++) {
        headers[i].classList.remove('ascending', 'descending');
      }

      if (isAscending) {
        rows.reverse(); // Reverse the rows array for descending order
        expensesList.innerHTML = ''; // Clear the table body

        // Add descending class to the column header
        headers[column].classList.add('descending');
      } else {
        expensesList.innerHTML = ''; // Clear the table body

        // Add ascending class to the column header
        headers[column].classList.add('ascending');
      }

      rows.forEach(row => expensesList.appendChild(row));
    }

    // Function to fetch expenses data and populate the table
    function fetchExpenses() {
      fetch('expenses_api.php')
        .then(response => response.json())
        .then(expenses => {
          const expensesList = document.getElementById('expensesList');
          expensesList.innerHTML = '';

          expenses.forEach(expense => {
            const row = document.createElement('tr');
            row.innerHTML = `
              <td class="py-2 px-4">${expense.name}</td>
              <td class="py-2 px-4">${expense.amount}</td>
              <td class="py-2 px-4">
                <button class="px-2 py-1 bg-blue-500 text-white font-semibold rounded mr-2" onclick="editExpense(${expense.id}, '${expense.name}', ${expense.amount})">Edit</button>
                <button class="px-2 py-1 bg-red-500 text-white font-semibold rounded" onclick="deleteExpense(${expense.id})">Delete</button>
              </td>
            `;

            expensesList.appendChild(row);
          });
        });
    }

    // Function to submit the form and add a new expense
    document.getElementById('addExpenseForm').addEventListener('submit', (event) => {
      event.preventDefault();

      const form = event.target;
      const name = form.elements.name.value;
      const amount = form.elements.amount.value;

      fetch('expenses_api.php', {
          method: 'POST',
          body: new URLSearchParams({
            name,
            amount
          })
        })
        .then(response => response.text())
        .then(message => {
          console.log(message);
          form.reset();
          fetchExpenses();
        });
    });

    // Function to edit an expense
    function editExpense(id, name, amount) {
      const newName = prompt('Enter new name:', name);
      const newAmount = prompt('Enter new amount:', amount);

      if (newName && newAmount !== null) {
        fetch('expenses_api.php', {
            method: 'PUT',
            body: new URLSearchParams({
              id,
              name: newName,
              amount: newAmount
            })
          })
          .then(response => response.text())
          .then(message => {
            console.log(message);
            fetchExpenses();
          });
      }
    }

    // Function to delete an expense
    function deleteExpense(id) {
      if (confirm('Are you sure you want to delete this expense?')) {
        fetch('expenses_api.php', {
            method: 'DELETE',
            body: new URLSearchParams({
              id
            })
          })
          .then(response => response.text())
          .then(message => {
            console.log(message);
            fetchExpenses();
          });
      }
    }

    // Fetch expenses when the page loads
    fetchExpenses();
  </script>
</body>

</html>
