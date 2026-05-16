// Get real data injected from the view
const data = window.dashboardData || { revenue: [], orders: [], topStores: [] };

// Revenue Chart
const revenueCtx = document.getElementById('revenueChart');
if (revenueCtx) {
  new Chart(revenueCtx, {
    type: 'line',
    data: {
      labels: data.revenue.map(item => item.month),
      datasets: [{
        label: 'Revenue',
        data: data.revenue.map(item => item.revenue),
        borderColor: 'rgb(99, 102, 241)',
        backgroundColor: 'rgba(99, 102, 241, 0.1)',
        tension: 0.4,
        fill: true
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: function (value) {
              return '$' + value.toLocaleString();
            }
          }
        }
      }
    }
  });
}

// Orders Chart
const ordersCtx = document.getElementById('ordersChart');
if (ordersCtx) {
  new Chart(ordersCtx, {
    type: 'line',
    data: {
      labels: data.orders.map(item => item.month),
      datasets: [{
        label: 'Orders',
        data: data.orders.map(item => item.count),
        borderColor: 'rgb(34, 197, 94)',
        backgroundColor: 'rgba(34, 197, 94, 0.1)',
        tension: 0.4,
        fill: true
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false
        }
      },
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });
}

// Top Stores Chart
const topStoresCtx = document.getElementById('topStoresChart');
if (topStoresCtx) {
  new Chart(topStoresCtx, {
    type: 'bar',
    data: {
      labels: data.topStores.map(item => item.store_name),
      datasets: [{
        label: 'Sales',
        data: data.topStores.map(item => item.revenue),
        backgroundColor: [
          'rgba(99, 102, 241, 0.8)',
          'rgba(34, 197, 94, 0.8)',
          'rgba(251, 191, 36, 0.8)',
          'rgba(239, 68, 68, 0.8)',
          'rgba(168, 85, 247, 0.8)',
          'rgba(236, 72, 153, 0.8)'
        ],
        borderRadius: 8
      }]
    },
    options: {
      indexAxis: 'y',
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false
        }
      },
      scales: {
        x: {
          beginAtZero: true,
          ticks: {
            callback: function (value) {
              return '$' + value.toLocaleString();
            }
          }
        }
      }
    }
  });
}
