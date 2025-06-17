<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Profil topSeller - ReUseMart</title>
  <!-- Bootstrap CSS -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
    crossorigin="anonymous"
  />
  <!-- Toastify CSS -->
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css"
  />
</head>
<body>

  @include('layouts.navbar')
  

  <main class="container my-5 p-4 bg-white rounded shadow-sm" style="max-width: 700px;">
    <h1 class="text-center text-success mb-3" id="dateNow">Top Seller Bulan ini</h1>

    <hr class="mb-4" />

    <h2 class="text-center text-success mb-4"></h2>

    <table class="table table-bordered w-75 mx-auto">
      <tbody>
        <tr>
          <th scope="row" class="w-25">ID Penitip</th>
          <td id="penitipId">-</td>
        </tr>
        <tr>
          <th scope="row">Nama Penitip</th>
          <td id="penitipNama">-</td>
        </tr>
        <tr>
          <th scope="row">Nominal</th>
          <td id="Nominal">-</td>
        </tr>
        <tr>
        <th>Bulan</th>
        <td><input type="date" id="dateInput" class="form-control" /></td>
        </tr>
        <tr>
            <th></th>
        </tr>
        <tr>
          <th scope="row">Update</th>
          <td><button type="button" class="btn btn-primary" id="updateBtn">Update</button></td>
        </tr>
      </tbody>
      
    </table>
    
  </main>

  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <!-- Toastify JS -->
  <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

  <script>
  document.addEventListener("DOMContentLoaded", () => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content'); 
    // Fetch the top seller data based on the selected year.month
    async function fetchTopSellerById(idTopSeller) {
      try {
        const response = await fetch(`http://127.0.0.1:8000/api/topseller/${idTopSeller}`, {
          method: "GET",
          headers: { "Authorization": `Bearer ${localStorage.getItem('auth_token')}` },
        });

        // Check if response is OK
        if (!response.ok) {
          throw new Error("Failed to fetch data");
        }

        const data = await response.json();

        // Render the data on the page
        renderData(data);
      } catch (error) {
        console.error("Error fetching Top Seller data:", error);
        Toastify({
          text: "An error occurred while fetching data.",
          duration: 3000,
          gravity: "top",
          position: "right",
          backgroundColor: "#ff4d4d",
        }).showToast();
      }
    }

    // Function to render data to the page
    function renderData(data) {
      document.getElementById("penitipId").textContent = data.idPenitip || "-";
      document.getElementById("penitipNama").textContent = data.namaPenitip || "-";
      document.getElementById("Nominal").textContent = new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR"
      }).format(data.nominal || 0);

    }

    async function fetchTopPenitipByMonth(month, year) {
      try {
        // Make an API call to the server to get the top penitip data for the specified month and year
        const response = await fetch(`http://127.0.0.1:8000/api/topseller?month=${month}&year=${year}`, {
          method: "GET",
          headers: {
            "Authorization": `Bearer ${localStorage.getItem('auth_token')}`,
            'X-CSRF-TOKEN': csrfToken,
          },
        });

        if (!response.ok) {
          throw new Error("Failed to fetch Top Penitip data");
        }

        // Parse the response
        const data = await response.json();

        // Return the fetched data
        return data;
      } catch (error) {
        console.error("Error fetching Top Penitip data:", error);
        Toastify({
          text: "An error occurred while fetching data.",
          duration: 3000,
          gravity: "top",
          position: "right",
          backgroundColor: "#ff4d4d",
        }).showToast();
      }
    }

    // Event listener for date input change to update the title with selected month and year
    document.getElementById("dateInput").addEventListener("change", function () {
      const selectedDate = new Date(this.value);
      const selectedMonth = String(selectedDate.getMonth() + 1).padStart(2, '0'); // Ensure 2-digit month
      const selectedYear = selectedDate.getFullYear();
      
      // Update the title with the selected month and year
      const selectedMonthYear = `${selectedYear}.${selectedMonth}`;
      document.getElementById("dateNow").textContent = `Top Seller Bulan ${new Intl.DateTimeFormat("id-ID", {
        month: "long",
        year: "numeric",
      }).format(selectedDate)}`;

      // Fetch the top seller data based on the selected month and year
      fetchTopSellerById(selectedMonthYear);
    });

    document.getElementById("updateBtn").addEventListener("click", async function () {
        const selectedDate = new Date(document.getElementById("dateInput").value);
        const selectedMonth = currentMonth;
        const selectedYear = currentYear;

        // Fetch the top penitip data for the selected month and year
        const topPenitipData = await fetchTopPenitipByMonth(selectedMonth, selectedYear);
        console.log(topPenitipData);
        if (!topPenitipData) return; // If no data found, exit

        // Extract necessary data
        const { idPenitip, totalPendapatan: nominal } = topPenitipData;

        // Prepare the data for the store API call
        const dataToSend = {
          idPenitip,
          nominal,
          month: selectedMonth,
          year: selectedYear,
        };
        console.log("b4 masuk api : ",dataToSend);

        // Call the store function in TopSellerController
        try {
          const storeResponse = await fetch("http://127.0.0.1:8000/api/topseller/add", {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
              "Authorization": `Bearer ${localStorage.getItem('auth_token')}`,
              'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify(dataToSend),
          });

          if (!storeResponse.ok) {
            throw new Error("Failed to store Top Seller data");
          }

          const storeData = await storeResponse.json();
          console.log("Top Seller stored successfully:", storeData);

          // You can now display a success toast or perform any other UI updates
          Toastify({
            text: "Top Seller updated successfully!",
            duration: 3000,
            gravity: "top",
            position: "right",
            backgroundColor: "#4CAF50",
          }).showToast();
          fetchTopSellerById(defaultMonthYear);

        } catch (error) {
          console.error("Error storing Top Seller data:", error);
          Toastify({
            text: "Failed to update Top Seller.",
            duration: 3000,
            gravity: "top",
            position: "right",
            backgroundColor: "#ff4d4d",
          }).showToast();
        }
      });

    // Initial fetch of top seller data with default date (current month)
    const currentDate = new Date();
    const currentMonth = String(currentDate.getMonth() + 1).padStart(2, '0'); // Ensure 2-digit month
    const currentYear = currentDate.getFullYear();
    const defaultMonthYear = `${currentYear}.${currentMonth}`; // Format as year.month
    console.log(defaultMonthYear);
    // Set the default date to the first of the current month
    document.getElementById("dateInput").value = `${currentYear}-${currentMonth}-01`;

    // Fetch the top seller data for the default (current month)
    fetchTopSellerById(defaultMonthYear);

  });
</script>
</body>
</html>
