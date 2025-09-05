

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Drought Prediction</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root {
      --primary: #0f766e;
      --primary-dark: #0d9488;
      --secondary: #64748b;
      --light: #f8fafc;
      --card-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
      --transition: all 0.3s ease;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
      background: linear-gradient(135deg, #f0f4f8 0%, #e2e8f0 100%);
      color: #334155;
      padding: 20px;
      min-height: 100vh;
    }

    .container {
      max-width: 1200px;
      margin: 0 auto;
    }

    .header {
      text-align: center;
      margin-bottom: 30px;
      animation: fadeIn 0.8s ease-out;
    }

    .logo {
      font-size: 2.5rem;
      color: var(--primary);
      margin-bottom: 15px;
    }

    h1 {
      font-size: 2.2rem;
      font-weight: 700;
      color: #1e293b;
      margin-bottom: 10px;
    }

    .subtitle {
      font-size: 1.1rem;
      color: var(--secondary);
      max-width: 600px;
      margin: 0 auto;
      line-height: 1.6;
    }

    .card {
      background: white;
      border-radius: 16px;
      padding: 25px;
      box-shadow: var(--card-shadow);
      margin-bottom: 25px;
      animation: fadeIn 0.5s ease-out;
    }

    .form-container {
      display: flex;
      flex-wrap: wrap;
      gap: 15px;
      align-items: end;
    }

    .form-group {
      flex: 1;
      min-width: 200px;
    }

    label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
      color: #374151;
    }

    select,
    input {
      width: 100%;
      padding: 12px 15px;
      border: 1px solid #e2e8f0;
      border-radius: 10px;
      font-size: 1rem;
      background: white;
      transition: var(--transition);
    }

    select:focus,
    input:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.15);
    }

    button {
      background: var(--primary);
      color: white;
      border: none;
      padding: 12px 24px;
      border-radius: 10px;
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      transition: var(--transition);
      height: 44px;
    }

    button:hover {
      background: var(--primary-dark);
      transform: translateY(-2px);
    }

    .status {
      color: var(--secondary);
      font-size: 0.9rem;
      margin-top: 10px;
      text-align: center;
    }

    .kpi-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 15px;
      margin-top: 20px;
    }

    .kpi {
      background: #f8fafc;
      border-radius: 12px;
      padding: 20px;
      text-align: center;
      transition: var(--transition);
    }

    .kpi:hover {
      transform: translateY(-3px);
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .kpi-title {
      font-size: 0.9rem;
      color: var(--secondary);
      margin-bottom: 10px;
    }

    .kpi-value {
      font-size: 1.5rem;
      font-weight: 700;
      color: #1e293b;
    }

    .risk-badge {
      padding: 6px 12px;
      border-radius: 20px;
      font-weight: 600;
      display: inline-block;
    }

    .Low {
      background-color: #dcfce7;
      color: #166534;
    }

    .Moderate {
      background-color: #fef3c7;
      color: #92400e;
    }

    .High {
      background-color: #fee2e2;
      color: #991b1b;
    }

    table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0;
      margin-top: 20px;
      background: white;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: var(--card-shadow);
    }

    th {
      background-color: #f1f5f9;
      padding: 16px;
      text-align: left;
      font-weight: 600;
      color: #374151;
      border-bottom: 2px solid #e2e8f0;
    }

    td {
      padding: 16px;
      border-bottom: 1px solid #e2e8f0;
    }

    tr:last-child td {
      border-bottom: none;
    }

    tr {
      transition: var(--transition);
    }

    tr:hover {
      background-color: #f8fafc;
    }

    .error-message {
      color: #dc2626;
      background: #fef2f2;
      padding: 12px;
      border-radius: 8px;
      margin: 15px 0;
      display: none;
      text-align: center;
    }

    .loading {
      display: none;
      text-align: center;
      margin: 20px 0;
    }

    .loading-spinner {
      border: 4px solid #f3f3f3;
      border-top: 4px solid var(--primary);
      border-radius: 50%;
      width: 40px;
      height: 40px;
      animation: spin 1s linear infinite;
      margin: 0 auto;
    }

    @keyframes spin {
      0% {
        transform: rotate(0deg);
      }

      100% {
        transform: rotate(360deg);
      }
    }

    /* Animations */
    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(20px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Responsive design */
    @media (max-width: 768px) {
      h1 {
        font-size: 1.8rem;
      }

      .subtitle {
        font-size: 1rem;
      }

      .form-container {
        flex-direction: column;
        align-items: stretch;
      }

      .form-group {
        width: 100%;
      }

      table {
        display: block;
        overflow-x: auto;
      }
    }

    @media (max-width: 480px) {
      h1 {
        font-size: 1.6rem;
      }

      .logo {
        font-size: 2rem;
      }

      .card {
        padding: 20px;
      }

      th,
      td {
        padding: 12px 10px;
        font-size: 0.9rem;
      }

      .kpi-container {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>
<div class="container">
  <div class="header">
    <div class="logo"><i class="fas fa-sun"></i></div>
    <h1>Drought Prediction</h1>
    <p class="subtitle">Select a country and get today + 7-day drought risk assessment</p>
  </div>

  <div class="card">
    <div class="form-container">
      <div class="form-group">
        <label for="country">Country</label>
        <select id="country" name="country">
          {% for c in countries %}
          <option value="{{ c }}">{{ c }}</option>
          {% endfor %}
        </select>
      </div>

      <div class="form-group">
        <label for="horizon">Horizon (days)</label>
        <input id="horizon" type="number" min="3" max="10" value="7">
      </div>

      <div class="form-group">
        <button type="button" id="predict-btn"><i class="fas fa-chart-line"></i> Predict</button>
      </div>

      <div class="form-group">
        <button type="button" id="save-btn"><i class="fas fa-save"></i> Save Your Prediction</button>
      </div>
    </div>

    <div id="status" class="status">Select a country and get today + 7-day drought risk.</div>

    <div id="error-message" class="error-message">
      <i class="fas fa-exclamation-circle"></i> <span id="error-text"></span>
    </div>

    <div id="loading" class="loading">
      <div class="loading-spinner"></div>
      <p>Processing your request...</p>
    </div>
  </div>

  <div class="card" id="today-card" style="display:none;">
    <h3>Today's Snapshot (<span id="today-date"></span>)</h3>
    <div class="kpi-container">
      <div class="kpi"><div class="kpi-title">Rainfall last 30d</div><div class="kpi-value" id="kpi-rain">–</div></div>
      <div class="kpi"><div class="kpi-title">Avg Temp last 7d</div><div class="kpi-value" id="kpi-temp">–</div></div>
      <div class="kpi"><div class="kpi-title">ET₀ last 30d</div><div class="kpi-value" id="kpi-et0">–</div></div>
      <div class="kpi"><div class="kpi-title">Probability</div><div class="kpi-value" id="kpi-prob">–</div></div>
      <div class="kpi"><div class="kpi-title">Risk Level</div><div id="kpi-risk" class="kpi-value risk-badge">–</div></div>
    </div>
  </div>

  <div class="card" id="timeline-card" style="display:none;">
    <h3>Next Days – Risk Timeline</h3>
    <table id="timeline-table">
      <thead>
        <tr>
          <th>Date</th>
          <th>Rain (30d sum, mm)</th>
          <th>Temp (7d avg, °C)</th>
          <th>ET₀ (30d sum, mm)</th>
          <th>Probability</th>
          <th>Risk</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
</div>

<script>
function fmt(n){return(n===null||n===undefined)?"–":(Math.round(n*100)/100).toString();}
function applyRiskClass(el,risk){el.classList.remove("Low","Moderate","High");if(["Low","Moderate","High"].includes(risk))el.classList.add(risk);}
function showError(msg){const e=document.getElementById('error-message');document.getElementById('error-text').textContent=msg;e.style.display='block';setTimeout(()=>{e.style.display='none';},5000);}
function showLoading(){document.getElementById('loading').style.display='block';}
function hideLoading(){document.getElementById('loading').style.display='none';}

let latestPrediction = null;

// Predict button
document.getElementById('predict-btn').addEventListener('click', function() {
  const country = document.getElementById('country').value;
  const horizon = parseInt(document.getElementById('horizon').value);

  if(!country){showError('Please select a country');return;}
  showLoading();
  document.getElementById('error-message').style.display='none';

  const formData = new FormData();
  formData.append('country', country);
  formData.append('horizon_days', horizon);

  fetch("/predict-country",{method:"POST",body:formData})
  .then(r=>{if(!r.ok)throw new Error('Network response was not ok');return r.json();})
  .then(data=>{
    hideLoading();
    if(data.error){showError(data.error);return;}
    latestPrediction=data; // store for save

    // Today snapshot
    const today=data.today;
    if(today){
      document.getElementById('today-card').style.display='block';
      document.getElementById('today-date').textContent=today.date;
      document.getElementById('kpi-rain').textContent=fmt(today.RainfallLast30Days);
      document.getElementById('kpi-temp').textContent=fmt(today.TemperatureAvg7);
      document.getElementById('kpi-et0').textContent=fmt(today.ET0Last30Days);
      document.getElementById('kpi-prob').textContent=fmt(today.probability);
      const riskEl=document.getElementById('kpi-risk');
      riskEl.textContent=today.risk_level;
      applyRiskClass(riskEl,today.risk_level);
    }

    // Timeline
    const tbody=document.querySelector("#timeline-table tbody");
    tbody.innerHTML="";
    (data.timeline||[]).forEach(row=>{
      const tr=document.createElement("tr");
      tr.innerHTML=`<td>${row.date}</td><td>${fmt(row.RainfallLast30Days)}</td><td>${fmt(row.TemperatureAvg7)}</td><td>${fmt(row.ET0Last30Days)}</td><td>${fmt(row.probability)}</td><td><span class="risk-badge ${row.risk_level}">${row.risk_level}</span></td>`;
      tbody.appendChild(tr);
    });
    document.getElementById('timeline-card').style.display='block';
  })
  .catch(err=>{hideLoading();console.error(err);showError('Request failed. Please try again.');});
});

document.getElementById('save-btn').addEventListener('click', function() {
  if (!latestPrediction) {
    showError('No prediction to save. Please run prediction first.');
    return;
  }

  showLoading();
  fetch("/save-prediction", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(latestPrediction)
  })
  .then(res => res.json())
  .then(res => {
    hideLoading();
    if (res.success) {
      alert("Prediction saved successfully! ID: " + res.prediction_id);
    } else {
      showError("Save failed: " + res.error);
    }
  })
  .catch(err => {
    hideLoading();
    console.error("Save request error:", err);
    showError('Save request failed');
  });
});

</script>

</body>
</html>
