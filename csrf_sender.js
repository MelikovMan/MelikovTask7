window.addEventListener('DOMContentLoaded',(event)=>{
    fetch("./csrf-handler.php",{
		method:"POST",
		body: new URLSearchParams({
      		param: '<?php if(isset($_SESSION['login'])) echo $_SESSION['login'];
			  else echo ''; ?>',
    	})
	}).
	then((response)=>{
		if(!response.ok){
			throw new Error(response.status);
		}
		return response;
	})
	.then((response)=>response.text())
	.then((response)=>{
		console.log("Request successful");
		console.log(response);
		document.getElementById('token').value = response;
	})
	.catch((error)=>{
		console.log("Can't send info!");
		console.log(error);
	});
});