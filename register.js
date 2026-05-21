
document.getElementById("save").addEventListener("click", check);

function check(event){

     event.preventDefault();

let fullname=document.getElementById("fullname").value.trim();
let ID_Number=document.getElementById("idNo").value.trim();
let phoneNumber=document.getElementById("phonenumber").value.trim();
let Username=document.getElementById("username").value.trim();
let password=document.getElementById("password").value.trim();
let Confirm=document.getElementById("confirm").value.trim();

if(fullname===""){
   
    alert("Please fill in your Fullname");

}else if(ID_Number===""){

    alert("Fill in the ID Number");

} else if(phoneNumber===""){

alert("Fill in the Phone Number");

}else if(Username===""){

    alert("Fill in your Username");

}else if(password===""){

    alert("Fill in Your Password");

}else if(password.length<7){

alert("The password should atleast be 8 characters!");

}else if(Confirm===""){

alert("Please  Confirm your Password");

}else if(password!=Confirm){

    alert("Passwords do not match");

}else{

    alert("Successfully registered!!!");
}
}
