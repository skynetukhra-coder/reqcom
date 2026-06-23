function loadSerials() {
    let section = document.getElementById("section").value;
    let device = document.getElementById("device") ? document.getElementById("device").value : "";

    if (section === "") {
        document.getElementById("serial").innerHTML = "<option>Select Serial</option>";
        return;
    }

    let url = "fetch_serial.php?section=" + encodeURIComponent(section);

    if (device !== "") {
        url += "&device=" + encodeURIComponent(device);
    }

    fetch(url)
        .then(res => res.text())
        .then(data => {
            document.getElementById("serial").innerHTML = data;
        });
}


/* 🔥 FIXED COMPLAINT LOADER */
function loadComplaints() {

    let deviceRaw = document.getElementById("device").value;

    /* Normalize */
    let device = deviceRaw.trim().toLowerCase();

    let complaints = {
        "monitor": ["Display issue", "No power", "Flickering"],
        "ups": ["Battery issue", "Not charging"],
        "printer": ["Paper jam", "Ink issue", "Not printing"],
        "desktop": ["Slow", "Not starting"],
        "laptop": ["Battery issue", "Overheating"],
        "scanner": ["Scan error"],
        "projector": ["No display"],
        "server": ["Down", "Network issue"]
    };

    let dropdown = document.getElementById("complaint");

    /* Reset dropdown */
    dropdown.innerHTML = "<option value=''>Select Complaint</option>";

    if (complaints[device]) {
        complaints[device].forEach(c => {
            dropdown.innerHTML += `<option value="${c}">${c}</option>`;
        });
    }

    /* Always add fallback */
    dropdown.innerHTML += `<option value="other">If any, mention</option>`;
}


/* REMARKS TOGGLE */
function toggleRemarks() {
    let val = document.getElementById("complaint").value;
    let remarks = document.getElementById("remarks");

    if (val === "other") {
        remarks.disabled = false;
    } else {
        remarks.disabled = true;
        remarks.value = "";
    }
}