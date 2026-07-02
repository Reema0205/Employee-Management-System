let selectedRow = null;
let rowCount = 1;

function addLeave() {

    let empId = document.getElementById("empId").value;
    let leaveType = document.getElementById("leaveType").value;
    let startDate = document.getElementById("startDate").value;
    let endDate = document.getElementById("endDate").value;
    let reason = document.getElementById("reason").value;

    if (
        empId === "" ||
        leaveType === "" ||
        startDate === "" ||
        endDate === "" ||
        reason === ""
    ) {
        alert("Fill all fields");
        return;
    }

    let formData = new FormData();

    formData.append("employee_id", empId);
    formData.append("leave_type", leaveType);
    formData.append("start_date", startDate);
    formData.append("end_date", endDate);
    formData.append("reason", reason);

    fetch("save_leave.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => {

        if (data.trim() === "success") {
            alert("Leave Applied Successfully");
            loadLeaves();
            clearInputs();
        } else {
            alert("Database Error");
        }

    });
}

// SELECT ROW
function addRowClick(){
    let table = document.getElementById("leaveTable");
    for(let i=1; i<table.rows.length; i++){
        table.rows[i].onclick = function(){
            if(selectedRow){
                selectedRow.classList.remove("selected");
            }
            selectedRow = this;
            selectedRow.classList.add("selected");
        }
    }
}

// APPROVE
function approveLeave(){
    if(selectedRow){
        let id = selectedRow.cells[0].innerText;

        fetch("update_status.php",{
            method:"POST",
            headers:{"Content-Type":"application/x-www-form-urlencoded"},
            body:"id=" + id + "&status=Approved"
        })
 .then(response => response.text())
 .then(data => {
            if(data.trim() === "success"){
                selectedRow.cells[6].innerHTML = `<span class="approved">Approved</span>`;
            } else {
                alert("DB Update Failed: " + data);
            }
        });
    }else{
        alert("Select a row first");
    }
}

// REJECT
function rejectLeave(){
    if(selectedRow){
        let id = selectedRow.cells[0].innerText;

        fetch("update_status.php",{
            method:"POST",
            headers:{"Content-Type":"application/x-www-form-urlencoded"},
            body:"id=" + id + "&status=Rejected"
        })
 .then(response => response.text())
 .then(data => {
            if(data.trim() === "success"){
                selectedRow.cells[6].innerHTML = `<span class="rejected">Rejected</span>`;
            } else {
                alert("DB Update Failed: " + data);
            }
        });
    }else{
        alert("Select a row first");
    }
}

// DELETE SELECTED ROW
function deleteRow(){

    if(selectedRow){

        let id = selectedRow.cells[0].innerText;

        fetch("delete_leave.php",{
            method:"POST",
            headers:{
                "Content-Type":"application/x-www-form-urlencoded"
            },
            body:"id=" + id
        })
        .then(response => response.text())
        .then(data => {

            if(data.trim() === "success"){
                alert("Record Deleted Successfully");
                loadLeaves();
                selectedRow = null;
            }else{
                alert("Delete Failed");
            }

        });

    }else{
        alert("Select a row first");
    }
}

// CLEAR ALL TABLE
function clearTable(){
    document.getElementById("leaveTable").innerHTML = `
        <tr>
            <th>ID</th>
            <th>Employee</th>
            <th>Type</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Reason</th>
            <th>Status</th>
        </tr>
    `;
    localStorage.removeItem("leaveData");
    rowCount = 1;
    selectedRow = null;
} 

// CLEAR INPUT FIELDS
function clearInputs(){
    document.getElementById("empId").value = "";
    document.getElementById("leaveType").value = "";
    document.getElementById("startDate").value = "";
    document.getElementById("endDate").value = "";
    document.getElementById("reason").value = "";
}

// SAVE TO LOCAL STORAGE
function saveTableData(){
    let table = document.getElementById("leaveTable");
    let data = [];
    for(let i=1; i<table.rows.length; i++){
        data.push(table.rows[i].innerHTML);
    }
    localStorage.setItem("leaveData", JSON.stringify(data));
    alert("Data saved successfully!");
}

// LOAD DATA ON PAGE LOAD
window.onload = function () {
    loadLeaves();
};

// UPDATE ROW COUNT AFTER DELETE
function updateRowCount(){
    let table = document.getElementById("leaveTable");
    rowCount = table.rows.length;
    for(let i=1; i<table.rows.length; i++){
        table.rows[i].cells[0].innerText = i;
    }
}

function loadLeaves() {

    fetch("get_leaves.php")
    .then(response => response.json())
    .then(data => {

        let table = document.getElementById("leaveTable");

        table.innerHTML = `
        <tr>
            <th>ID</th>
            <th>Employee</th>
            <th>Type</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Reason</th>
            <th>Status</th>
        </tr>
        `;

        data.forEach(item => {

            let row = table.insertRow();

            row.innerHTML = `
                <td>${item.id}</td>
                <td>${item.employee_id}</td>
                <td>${item.leave_type}</td>
                <td>${item.start_date}</td>
                <td>${item.end_date}</td>
                <td>${item.reason}</td>
                <td>${item.status}</td>
            `;
        });

        addRowClick();
    });
}  