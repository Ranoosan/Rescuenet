<?php 
session_start(); 
if(!isset($_SESSION['user_id'])){ 
    header("Location: login.php"); 
    exit(); 
} 
require_once "db.php";
?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Advanced Disaster Prediction</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .step { display: none; }
        .step.active { display: block; }
        .progress { height: 20px; }
        .progress-bar { transition: width 0.3s; }
        .custom-tip { background-color: #f8f9fa; border-left: 4px solid #0d6efd; padding: 15px; margin-bottom: 15px; border-radius: 4px; }
        .emergency-contact { background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin-bottom: 15px; border-radius: 4px; }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h2 class="text-center mb-4">Disaster Risk Prediction</h2>
        
        <!-- Progress Bar -->
        <div class="progress mb-4">
            <div class="progress-bar bg-success" role="progressbar" style="width: 0%" id="progressBar"></div>
        </div>
        
        <!-- Multi-Step Form -->
        <form id="disasterForm" class="bg-white p-4 rounded shadow">
            <!-- Step 1: Disaster Type -->
            <div class="step active" data-step="1">
                <h5>Step 1: Disaster Selection</h5>
                <div class="mb-3">
                    <label for="disaster_type" class="form-label">Disaster Type</label>
                    <select id="disaster_type" class="form-select">
                        <option>Flood</option>
                        <option>Cyclone</option>
                        <option>Landslide</option>
                        <option>Drought</option>
                        <option>Earthquake</option>
                    </select>
                </div>
                <button type="button" class="btn btn-primary" onclick="nextStep()">Next</button>
            </div>
            
            <!-- Step 2: Location Info -->
            <div class="step" data-step="2">
                <h5>Step 2: Location Info</h5>
                <div class="mb-3">
                    <label class="form-label">Where is your home?</label>
                    <select id="location_type" class="form-select">
                        <option>Coastal</option>
                        <option>Hillside</option>
                        <option>Plains</option>
                        <option>Urban</option>
                        <option>Rural</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Is your neighborhood crowded?</label>
                    <select id="crowded1" class="form-select">
                        <option>Low</option>
                        <option>Medium</option>
                        <option>High</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Approximate number of houses around you?</label>
                    <select id="crowded2" class="form-select">
                        <option>Few</option>
                        <option>Some</option>
                        <option>Many</option>
                    </select>
                </div>
                <button type="button" class="btn btn-secondary" onclick="prevStep()">Previous</button>
                <button type="button" class="btn btn-primary" onclick="nextStep()">Next</button>
            </div>
            
            <!-- Step 3: Environmental Info -->
            <div class="step" data-step="3">
                <h5>Step 3: Environmental Conditions</h5>
                <div class="mb-3">
                    <label class="form-label">How is the weather today?</label>
                    <select id="weather1" class="form-select">
                        <option>Sunny</option>
                        <option>Cloudy</option>
                        <option>Rainy</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">How do you feel about the temperature?</label>
                    <select id="weather2" class="form-select">
                        <option>Hot</option>
                        <option>Warm</option>
                        <option>Cool</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Wind today?</label>
                    <select id="wind1" class="form-select">
                        <option>Calm</option>
                        <option>Breezy</option>
                        <option>Windy</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Do you see trees swaying?</label>
                    <select id="wind2" class="form-select">
                        <option>No</option>
                        <option>Somewhat</option>
                        <option>Strongly</option>
                    </select>
                </div>
                <button type="button" class="btn btn-secondary" onclick="prevStep()">Previous</button>
                <button type="button" class="btn btn-primary" onclick="nextStep()">Next</button>
            </div>
            
            <!-- Step 4: Vulnerability -->
            <div class="step" data-step="4">
                <h5>Step 4: Disaster Vulnerability</h5>
                <div class="mb-3">
                    <label class="form-label">Do you feel your area is prone to disasters?</label>
                    <select id="vul1" class="form-select">
                        <option>Low</option>
                        <option>Medium</option>
                        <option>High</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Have disasters occurred here before?</label>
                    <select id="vul2" class="form-select">
                        <option>Low</option>
                        <option>Medium</option>
                        <option>High</option>
                    </select>
                </div>
                <button type="button" class="btn btn-secondary" onclick="prevStep()">Previous</button>
                <button type="submit" class="btn btn-success">Predict Severity</button>
                
            </div>
        </form>
        
        <div class="mt-4 bg-white p-3 rounded shadow" id="result"></div>
    </div>

    <script>
        let currentStep = 1;
        const totalSteps = document.querySelectorAll('.step').length;
        const progressBar = document.getElementById('progressBar');
        
        function showStep(step){
            document.querySelectorAll('.step').forEach((s,i)=>{
                s.classList.toggle('active', i+1===step);
            });
            progressBar.style.width = ((step-1)/totalSteps*100)+'%';
        }
        
        function nextStep(){
            if(currentStep<totalSteps){
                currentStep++;
                showStep(currentStep);
            }
        }
        
        function prevStep(){
            if(currentStep>1){
                currentStep--;
                showStep(currentStep);
            }
        }
        
        // Map multiple human-friendly answers to ML inputs
        function mapAnswersToML(){
            const loc = document.getElementById('location_type').value;
            const coordMap = {
                "Coastal": {lat:6.9271, lon:79.8612, elev:5}, 
                "Hillside": {lat:7.0, lon:80.5, elev:150}, 
                "Plains": {lat:8.0, lon:80.0, elev:50}, 
                "Urban": {lat:6.95, lon:79.85, elev:10}, 
                "Rural": {lat:7.1, lon:80.1, elev:50}
            };
            const elevation_m = coordMap[loc].elev;
            const latitude = coordMap[loc].lat;
            const longitude = coordMap[loc].lon;
            
            const crowd1 = document.getElementById('crowded1').value;
            const crowd2 = document.getElementById('crowded2').value;
            const popMap = {"Low":500,"Medium":1500,"High":3500,"Few":500,"Some":1500,"Many":3500};
            const population_density = Math.round((popMap[crowd1]+popMap[crowd2])/2);
            
            const weather1 = document.getElementById('weather1').value;
            const weather2 = document.getElementById('weather2').value;
            const tempMap = {"Hot":30,"Warm":27,"Cool":22,"Sunny":30,"Cloudy":27,"Rainy":22};
            const avg_rainfall_mm = Math.round((weather1==="Rainy"?400:(weather1==="Cloudy"?250:200)));
            const temp_c = Math.round((tempMap[weather1]+tempMap[weather2])/2);
            
            const wind1 = document.getElementById('wind1').value;
            const wind2 = document.getElementById('wind2').value;
            const windMap = {"Calm":10,"Breezy":20,"Windy":40,"No":10,"Somewhat":20,"Strongly":40};
            const wind_speed_kmh = Math.max(windMap[wind1], windMap[wind2]);
            
            const vul1 = document.getElementById('vul1').value;
            const vul2 = document.getElementById('vul2').value;
            const vulMap = {"Low":"Low","Medium":"Medium","High":"High"};
            const vulnerability_index = (vulMap[vul1]==="High"||vulMap[vul2]==="High")?"High":(vulMap[vul1]==="Medium"||vulMap[vul2]==="Medium")?"Medium":"Low";
            
            return {
                disaster_type: document.getElementById('disaster_type').value,
                location_type: loc,
                latitude,
                longitude,
                elevation_m,
                population_density,
                avg_rainfall_mm,
                wind_speed_kmh,
                temp_c,
                vulnerability_index
            };
        }
        
        // Enhanced function to generate customized tips
        function generateCustomizedTips(data, severity) {
            let tips = [];
            
            // General tips based on severity
            if (severity === "Severe") {
                tips.push("EVACUATE IMMEDIATELY: Follow designated evacuation routes to safe zones.");
                tips.push("EMERGENCY KIT: Grab your pre-prepared emergency kit with essentials.");
            } else if (severity === "Moderate") {
                tips.push("PREPARE TO EVACUATE: Have your emergency kit ready and monitor updates.");
                tips.push("SECURE PROPERTY: Reinforce doors and windows, secure outdoor items.");
            } else {
                tips.push("STAY VIGILANT: Continue monitoring local news and weather updates.");
                tips.push("PREPARE SUPPLIES: Ensure you have at least 3 days of water and non-perishable food.");
            }
            
            // Disaster-specific tips
            switch(data.disaster_type) {
                case "Flood":
                    tips.push("Move to higher ground immediately if in a flood-prone area.");
                    if (data.location_type === "Coastal") {
                        tips.push("Storm surge possible - seek elevated shelter.");
                    }
                    tips.push("Avoid walking or driving through flood waters.");
                    break;
                    
                case "Cyclone":
                    tips.push("Stay away from windows and seek shelter in the strongest part of your home.");
                    if (data.wind_speed_kmh > 30) {
                        tips.push("High winds expected - secure outdoor objects that could become projectiles.");
                    }
                    break;
                    
                case "Landslide":
                    if (data.location_type === "Hillside") {
                        tips.push("Be alert for unusual sounds like trees cracking or boulders knocking together.");
                    }
                    tips.push("Evacuate immediately if you notice signs of soil movement or cracks in the ground.");
                    break;
                    
                case "Drought":
                    tips.push("Conserve water by limiting non-essential use.");
                    tips.push("Protect against heat-related illnesses by staying hydrated and cool.");
                    break;
                    
                case "Earthquake":
                    tips.push("DROP, COVER, and HOLD ON during shaking.");
                    tips.push("After shaking stops, check for injuries and damage before moving.");
                    if (data.location_type === "Urban") {
                        tips.push("Be cautious of falling debris from buildings.");
                    }
                    break;
            }
            
            // Location-specific tips
            switch(data.location_type) {
                case "Coastal":
                    tips.push("Know your tsunami evacuation route if near the coast.");
                    break;
                    
                case "Hillside":
                    tips.push("Be aware of potential landslide risks, especially after heavy rain.");
                    break;
                    
                case "Urban":
                    tips.push("In densely populated areas, follow crowd control instructions from authorities.");
                    break;
                    
                case "Rural":
                    tips.push("If isolated, ensure you have sufficient supplies as help may take longer to arrive.");
                    break;
            }
            
            // Weather-specific tips
            if (data.weather1 === "Rainy") {
                tips.push("Heavy rainfall expected - avoid low-lying areas and never attempt to cross flooded roads.");
            }
            
            if (data.wind_speed_kmh > 25) {
                tips.push("High winds expected - secure loose outdoor items and stay indoors.");
            }
            
            // Vulnerability-based tips
            if (data.vulnerability_index === "High") {
                tips.push("Your area has high vulnerability - consider relocating to a safer location if possible.");
            }
            
            // Add timing context
            const hour = new Date().getHours();
            if (hour > 18 || hour < 6) {
                tips.push("As it's nighttime, ensure you have flashlights and backup power sources ready.");
            }
            
            return tips;
        }
        
        // Submit Form
        document.getElementById('disasterForm').addEventListener('submit', async function(e){
            e.preventDefault();
            const data = mapAnswersToML();
            
            try {
                // 1️⃣ Save input to DB
                const saveForm = new FormData();
                Object.keys(data).forEach(key => saveForm.append(key,data[key]));
                const saveResp = await fetch('save_input.php',{method:'POST',body:saveForm});
                const saveResult = await saveResp.json();
                if(!saveResult.success) throw new Error(saveResult.error);
                
                // 2️⃣ Call ML API
                const mlResponse = await fetch('http://127.0.0.1:5002/predict',{
                    method:'POST',
                    headers:{'Content-Type':'application/json'},
                    body:JSON.stringify(data)
                });
                
                if(!mlResponse.ok) throw new Error("ML API returned status "+mlResponse.status);
                const mlResult = await mlResponse.json();
                const severity = mlResult.severity;
                
                // 3️⃣ Get emergency contacts
                const formData = new FormData();
                formData.append('disaster_type', data.disaster_type);
                formData.append('location_type', data.location_type);
                const phpResponse = await fetch('get_emergency.php',{method:'POST',body:formData});
                const emergency = await phpResponse.json();
                
                // 4️⃣ Generate customized tips
                const tips = generateCustomizedTips(data, severity);
                
                // Display results
                let html = `<h5>Predicted Severity: <span class="badge bg-${severity==='Severe'?'danger':severity==='Moderate'?'warning':'info'}">${severity}</span></h5>`;
                
                html += `<div class="custom-tip"><strong>AI-Generated Customized Tips:</strong><ul>`;
                tips.forEach(tip => {
                    html += `<li>${tip}</li>`;
                });
                html += `</ul></div>`;
                
                html += `<div class="emergency-contact"><strong>Emergency Contacts:</strong><ul class="list-group list-group-flush">`;
                html += `<li class="list-group-item">Police: ${emergency.police}</li>`;
                html += `<li class="list-group-item">Fire: ${emergency.fire}</li>`;
                html += `<li class="list-group-item">Ambulance: ${emergency.ambulance}</li>`;
                html += `<li class="list-group-item">Hotline: ${emergency.hotline}</li>`;
                html += `<li class="list-group-item">Shelters: ${emergency.shelters}</li>`;
                html += `</ul></div>`;
                
                document.getElementById('result').innerHTML = html;
                
            } catch(err){
                console.error("Error:",err);
                alert("Something went wrong. Check console.");
            }
        });
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>