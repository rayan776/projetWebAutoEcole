function changeColor(id, color) {
	document.getElementById(id).style.backgroundColor = color;
}

function disableField(id, val) {
	document.getElementById(id).disabled = val;

	var bgColor;
	if (val)
		bgColor = "grey";
	else
		bgColor = "white";

	document.getElementById(id).style.backgroundColor = bgColor;
}

function toggleInput(id) {
	document.getElementById(id).disabled = !document.getElementById(id).disabled;
}

function toutCocher(val, classCheckboxes) {
	var cases = document.getElementsByClassName(classCheckboxes);
	
	var i;
	for (i=0; i<cases.length; i++) {
		cases[i].checked = val;
	}
}

function disableSelectRoleOuUser(type) {
		if (type == "afficherParUser") {
			document.getElementById("nomRole").disabled = true;
			document.getElementById("login").disabled = false;
		}
		else {
			document.getElementById("nomRole").disabled = false;
			document.getElementById("login").disabled = true;
		}
}

function disableInputsLimites() {
	var checkboxInfini = document.getElementById("infiniLimite");

	checkboxInfini.addEventListener('change', function() {
	  if (this.checked) {
	    var inputsLimit = document.getElementsByClassName("inputsLimit");
	    var i;
	    for (i=0; i<inputsLimit.length; i++) {
	    	inputsLimit[i].disabled = true;
	    }
	  } else {
	    var inputsLimit = document.getElementsByClassName("inputsLimit");
	    var i;
	    for (i=0; i<inputsLimit.length; i++) {
	    	inputsLimit[i].disabled = false;
	    }
	  }
	});

	var checkboxDelete = document.getElementById("supprimerLimite");
	
	if (checkboxDelete != null) {
		checkboxDelete.addEventListener('change', function() {
			if (this.checked) {
				var inputsLimit = document.getElementsByClassName("inputsLimit");
				var i;
				for (i=0; i<inputsLimit.length; i++) {
		    			inputsLimit[i].disabled = true;
		    		}
		    		document.getElementById("infiniLimite").disabled = true;
			}
			else {
				var inputsLimit = document.getElementsByClassName("inputsLimit");
				var i;
				for (i=0; i<inputsLimit.length; i++) {
		    			inputsLimit[i].disabled = false;
		    		}
		    		document.getElementById("infiniLimite").disabled = false;
			}
		});
	}
	
	
}

function changeColorAllSelectsEspaceMoniteur() {
	var x = document.getElementsByClassName("selectEval");
	
	var i;
	
	for (i=0; i<x.length; i++) {
		var selectVal = x[i].value;
			
			switch (selectVal) {
				case 'Non acquis':
					x[i].style.backgroundColor = "#ff9191";
					x[i].style.border = "1px solid darkred";
					break;
				case "En cours d'acquisition":
					x[i].style.backgroundColor = "orange";
					x[i].style.border = "1px solid #481f12";					
					break;
				case 'Acquis':
					x[i].style.backgroundColor = "lightgreen";
					x[i].style.border = "1px solid green";					
					break;
				default:
					x[i].style.backgroundColor = "white";
					x[i].style.border = "1px solid black";					
					break;
			}
	}
}

function espaceMoniteurSelectChangeColorListener() {

	changeColorAllSelectsEspaceMoniteur();
	
	var x = document.getElementsByClassName("selectEval");
	
	for (i=0; i<x.length; i++) {
		x[i].addEventListener('change', function() {
			
			var selectVal = this.value;
			
			switch (selectVal) {
				case 'Non acquis':
					this.style.backgroundColor = "#ff9191";
					this.style.border = "1px solid darkred";
					break;
				case "En cours d'acquisition":
					this.style.backgroundColor = "orange";
					this.style.border = "1px solid #481f12";					
					break;
				case 'Acquis':
					this.style.backgroundColor = "lightgreen";
					this.style.border = "1px solid green";					
					break;
				default:
					this.style.backgroundColor = "white";
					this.style.border = "1px solid black";					
					break;
			}
			
		});
	}

}

function appliquerProgToutesLesComps() {

	var checkboxes = document.getElementsByClassName("checkboxIdComp");
	
	var evals = [];
	
	for (var i=0; i<checkboxes.length; i++) {
		if (checkboxes[i].checked) {
			evals.push("eval" + checkboxes[i].value);
		}
	}
	
	var valueAAppliquerATous = document.getElementById("evalMultiples").value;
	
	for (i=0; i<evals.length; i++) {
		document.getElementById(evals[i]).value = valueAAppliquerATous;
	}
	
	changeColorAllSelectsEspaceMoniteur();
	
}

function resetRemarquesComps() {

	var checkboxes = document.getElementsByClassName("checkboxIdComp");
	
	var inputComps = [];
	
	for (var i=0; i<checkboxes.length; i++) {
		if (checkboxes[i].checked) {
			inputComps.push("inputComp" + checkboxes[i].value);
		}
	}
	
	var i;
	
	for (i=0; i<inputComps.length; i++) {
		document.getElementById(inputComps[i]).value = "";
	}
}

function changerRemarquesComps() {

	var checkboxes = document.getElementsByClassName("checkboxIdComp");
	
	var inputComps = [];
	
	for (var i=0; i<checkboxes.length; i++) {
		if (checkboxes[i].checked) {
			inputComps.push("inputComp" + checkboxes[i].value);
		}
	}
	
	var valChangement = document.getElementById("remarqueToutesLesComps").value;
	
	var i;
	for (i=0; i<inputComps.length; i++) {
		document.getElementById(inputComps[i]).value = valChangement;
	}
}


function padZero(v) {
    return (v < 10) ? "0" + v : v;
}

function newDate(date, days) {

  
  var dateArray = date.split('-').map(Number);
  
  var myDate = new Date(dateArray[0], dateArray[1] - 1, dateArray[2]);
  
  myDate = new Date(myDate.getTime() + days * 24 * 60 * 60 * 1000);
  
  return myDate.getFullYear() + '-' + padZero(myDate.getMonth() + 1) + '-' + padZero(myDate.getDate());
  
}

function textDate(date) {
	var dateArray = date.split('-').map(String);
	
	return dateArray[2] + "/" + dateArray[1] + "/" + dateArray[0];
}

function changerSem(add) {

	var days = (add==1) ? 7 : -7;

	var semaine = document.getElementById("sem");
	
	var nouvelleDate = newDate(semaine.value, days);
	var nouvelleDateText = textDate(nouvelleDate) + " - " + textDate(newDate(nouvelleDate, 7));
	
	const optionsSem = semaine.options;
	
	var dejaLa = 0;
	
	for (var i=0; i<optionsSem.length; i++) {
		if (optionsSem[i].value == nouvelleDate) {
			dejaLa=1;
			semaine.value = nouvelleDate;
			break;
		}
	}
	
	if (dejaLa==0) {
		let opt = new Option(nouvelleDateText, nouvelleDate);
		semaine.add(opt, undefined);
		semaine.value = nouvelleDate;
	}

}



function draw(id, timePassed, cacher) {

	if (cacher==false) {
	  id.style.opacity = (timePassed/100)*(100/13) + "%";
	}
	else {
	  id.style.opacity = 100-((timePassed/100)*(100/13)) + "%";
	  
	  if (id.style.opacity<0.1) {
	  	id.style.right=0;
		id.style.top=0;
	  }
	}
}

function voirCom(id, cacher) {


	let com = document.getElementById(id);
	
	if (cacher==true&&com.style.opacity<0.9) {
	
	}
	else {
			
		if (cacher==false) {
			com.style.right=event.pageX;
			com.style.top=event.pageY;
		}
		
		let start = Date.now();
		
		let timer = setInterval(function() {
			
			let timePassed=Date.now()-start;
			
			draw(com, timePassed, cacher);
			
			if (timePassed >= 1300) {
				clearInterval(timer);
				return;
			}
		
		
		
		}, 20);
	}
	
}


function initAjaxLogin() {

	  const specialChars = /[`!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~ ]/;

	   $("#login").keyup(function(){

	     var login = $(this).val().trim();
  	     
	     if(login != '' && login.length>=5 && login.length<=15){
	     
	     	let ajaxOk=1;
	     
	     	if (specialChars.test(login)) {
	     		$("#login").css('border','2px solid red');
		       $("#uname_response").css('color','red');
		       $("#uname_response").html("Votre login ne doit pas contenir de caractères spéciaux ou d'espaces.");
		       ajaxOk=0;
	     	}
	     	
	     	var queDesChiffres=1;
	     	
	     	for (var i=0; i<login.length; i++) {
	     		if (!Number.isInteger(Number.parseInt(login[i]),10)) {
	     			queDesChiffres=0;
	     		}
	     	}
	     	
	     	if (queDesChiffres==1) {
	     		$("#login").css('border','2px solid red');
		       $("#uname_response").css('color','red');
		       $("#uname_response").html("Votre login ne doit pas contenir que des chiffres.");
	     		ajaxOk=0;
	     	}
	     	
	     	if (ajaxOk==1) {
	     		$.ajax({
			   url: 'models/ajaxLoginExists.php',
			   type: 'post',
			   data: {login:login},
			   success: function(response){

			      $("#uname_response").html(response);
			      
			      if (response=="Ce login est disponible.") {
			      	$("#login").css('border','2px solid green');
			      	$("#uname_response").css('color','lightgreen');
			      }
			      else {
			       $("#login").css('border','2px solid red');
			       $("#uname_response").css('color','red');
			      }

			   }
			});
	     	}

		
	     }else{
	     	if (login.length>0&&login.length<5||login.length>15) {
	     		$("#uname_response").html("Votre login doit faire entre 5 et 15 caractères.");
	     		$("#login").css('border','2px solid red');
		       $("#uname_response").css('color','red');
	     	}
	     	else {
			$("#uname_response").html("");
			$(this).css('border','none');	
		}
	     }

	  });
  

}

function verifPwd() {
	
  	var password = $("#password").val().trim();
  	
  	var force = 0;
  	var chiffres = 0;
  	var maj = 0;
  	var min = 0;
  	const specialChars = /[`!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~]/;
  	
  	if(password != '') {
  		if (password.length>=8) {
  			force += 1;
  		}
  		for (var i=0; i<password.length; i++) {
  			if (Number.isInteger(Number.parseInt(password[i]),10)) {
  				chiffres += 1;
  			}
  			
			if (password[i] == password[i].toUpperCase()) {
				maj += 1;
				if (Number.isInteger(Number.parseInt(password[i]),10)||specialChars.test(password[i])) {
					maj -= 1;
				}
			}
  			if (password[i] == password[i].toLowerCase()) {
  				min += 1;
				if (Number.isInteger(Number.parseInt(password[i]),10)||specialChars.test(password[i])) {			
  					min -= 1;
  				}
  			}
		
  		}
  		
  		if (specialChars.test(password)) {
  			force += 1;
  		}
  		
  		if (chiffres > 2)
  			force += 1;
  		
  		if (maj>0&&min>0)
  			force += 1;
  		
  		var color;
  		var msg;
  		
  		if (force<=1) {
  			$("#pwd_reponse").css('color','red');
  			$("#password").css('border','2px solid red');
  			$("#pwd_reponse").html("Votre mot de passe est faible.");
  		}
  		else {
  			if (force<4) {
  				$("#pwd_reponse").css('color','orange');
  				$("#password").css('border','2px solid orange');  				
  				$("#pwd_reponse").html("Votre mot de passe est moyen.");
  			}
  			else {
  				$("#pwd_reponse").css('color','lightgreen');
  				$("#password").css('border','2px solid green');  				
  				$("#pwd_reponse").html("Votre mot de passe est conforme.");
  			}
  		}
  		
	}
  	else {
  		$("#pwd_reponse").html("");
  		$("#password").css('border','none');
  		$("#pwd_reponse").css('color','none');  		
  	}
}


function confirmPwd(deleteAccount) {

	if (deleteAccount==0) {
  		var password=$("#password").val().trim();
  	}
  	else {
  		var password=$("#mdpActu").val().trim();
  	}
  	
  	var confPwd=$("#newMdpConf").val().trim();
  	
  	
  	if (confPwd!=''&&password!=''&&password!=confPwd) {
  		$("#msgConfMdp").html("Les deux mot de passe ne sont pas identiques.");
  		$("#msgConfMdp").css("color", "red");
  		$("#newMdpConf").css('border', '2px solid red');
  	}
  	else {
  		if (password!=''&&confPwd!=''&&password==confPwd) {
  			$("#newMdpConf").css("border", "2px solid green");
  			$("#msgConfMdp").html("");
  			$("#msgConfMdp").css("color", "lightgreen");
  		}
  		else {
  			if (confPwd!=''&&password=='') {
  				$("#msgConfMdp").html("Vous n'avez pas saisi de mot de passe.");
  				$("#msgConfMdp").css("color", "red");  				
  				$("#newMdpConf").css("border", "2px solid red");
  			}
  			else {
  				$("#msgConfMdp").html("");
  				$("#newMdpConf").css("border", "none");
  			}
  		}
  	}

}

function initVerifPwd() {
	$("#password").keyup(function(){
  
  	
  	verifPwd();
  	
  	$("#newMdpConf").keyup(function() {
  		confirmPwd(0);
  	});
  	
  	confirmPwd(0);
  });
  

}


function getListLogins(inputLogin,parEleve) {

	var idLogin = "#" + inputLogin;

	$(idLogin).keyup(function() {

		var login = $(idLogin).val().trim();

		if(login != '') {	
			$.ajax({
			   url: 'models/ajaxSuggestionsLogin.php',
			   type: 'POST',
			   data: {login:login, inputName:inputLogin, chercherEleve:parEleve},
			   success: function(response){

				if (response!="offline") {
				      $(".suggestions").html(response);
				      $(".suggestions").css("opacity", '1.0');
				}
				else {
					$(".suggestions").html("");
					$(".suggestions").css("opacity", '0.0');
				}

			   }
			});
		}
		else {
			$(".suggestions").html("");
			$(".suggestions").css("opacity", '0.0');
		}
	});
}

function getListLoginsMsg(inputLogin,table) {

	var idLogin = "#" + inputLogin;

	$(idLogin).keyup(function() {

		var login = $(idLogin).val().trim();

		if(login != '') {	
			$.ajax({
			   url: 'models/ajaxAuteurMsg.php',
			   type: 'POST',
			   data: {table:table,login:login,inputName:inputLogin},
			   success: function(response){

			
			      $(".suggestionsMsg").html(response);
			      $(".suggestionsMsg").css("opacity", '1.0');

			   }
			});
		}
		else {
			$(".suggestionsMsg").html("");
			$(".suggestionsMsg").css("opacity", '0.0');
		}
	});
}


function getPwdActu(inputPwd, deleteAccount) {

	var idPwd = "#" + inputPwd;
	
	$(idPwd).keyup(function() {

		var pwd = $(idPwd).val().trim();

		if(pwd != '') {	
			$.ajax({
			   url: 'models/ajaxChangerMdp.php',
			   type: 'POST',
			   data: {mdpActu:pwd},
			   success: function(response){

				if (response=="0") {
					$("#msgMdpActu").html("Le mot de passe ne correspond pas à votre mot de passe actuel.");
					$("#msgMdpActu").css("color","red");	
					$("#mdpActu").css("border","2px solid red");							
				}
				else {
					$("#msgMdpActu").html("");
					$("#mdpActu").css("border","2px solid green");
				}

			   }
			});
		}
		else {
			$("#msgMdpActu").html("");
			$("#mdpActu").css("border","none");			
		}
	});
	
	var idPwd2 = (deleteAccount==0) ? "#password" : "#mdpActu";
	
	$(idPwd2).keyup(function() {
	
		if (deleteAccount==0) {
			verifPwd();
		}
		
		confirmPwd(deleteAccount);
	});
	
	$("#newMdpConf").keyup(function() {
		confirmPwd(deleteAccount);
	});
}


function changeInputLogin(inputName, login) {
	document.getElementById(inputName).value=login;
}


function enleverReport() {
	var reports = document.getElementById("reports");
	reports.style.opacity="0.0";
	
	var ier = setInterval(function() {
		clearInterval(ier);
		reports.innerHTML='';
	},100);
}
	
function reporter(idSeance,token) {
	
	var reports = document.getElementById("reports");
	
	reports.innerHTML='';
	
	var divReport = document.createElement("div");
	
	var formReport = document.createElement("form");
	formReport.setAttribute("method", "POST");
	formReport.setAttribute("action","index.php?module=ModMoniteur&action=gererReservations");
	
	var tokenInput = document.createElement("input");
	tokenInput.setAttribute("type","hidden");
	tokenInput.setAttribute("name","csrfToken");
	tokenInput.setAttribute("value",token);
	
	var idSeanceInput = document.createElement("input");
	idSeanceInput.setAttribute("type","hidden");
	idSeanceInput.setAttribute("name","idSeance");
	idSeanceInput.setAttribute("value",idSeance);
	
	var divFlexQuitter = document.createElement("div");
	divFlexQuitter.style.display="flex";
	
	var hReport = document.createElement("h4");
	hReport.innerHTML = "Reportez cette séance";
	
	var divQuitter = document.createElement("div");
	
	var boutonQuitter = document.createElement("button");
	boutonQuitter.setAttribute("type","button");
	boutonQuitter.setAttribute("class", "boutons");
	boutonQuitter.setAttribute("onclick","enleverReport();");
	boutonQuitter.setAttribute("id", "quitReport");
	boutonQuitter.innerHTML = "Quitter";
	
	divQuitter.appendChild(boutonQuitter);
	
	divFlexQuitter.appendChild(hReport);
	divFlexQuitter.appendChild(divQuitter);
	
	var divDateReport = document.createElement("div");
	
	var dateReport=document.createElement("input");
	
	dateReport.setAttribute("name", "dateReport");
	dateReport.setAttribute("type", "date");
	dateReport.style.marginLeft="70px";
	dateReport.style.width="125px";
	
	var labelDateReport = document.createElement("div");
	labelDateReport.innerHTML = "Date de report";
	labelDateReport.style.marginLeft="70px";
	
	divDateReport.appendChild(labelDateReport);
	divDateReport.appendChild(dateReport);
	
	var divHeureReport = document.createElement("div");
	
	var selectHeureReport = document.createElement("select");
	selectHeureReport.setAttribute("name", "heureReport");
	selectHeureReport.setAttribute("id", "selectHeureReport");
	
	for (var i=8; i<=18; i++) {
		var optHeure = document.createElement("option");
		optHeure.setAttribute("value", i);
		optHeure.innerHTML = i + "h";
		selectHeureReport.appendChild(optHeure);
	}
	
	var labelHeureReport = document.createElement("div");
	labelHeureReport.innerHTML = "Heure de report";
	labelHeureReport.style.marginLeft="65px";
	labelHeureReport.style.marginTop="30px";		
	
	divHeureReport.appendChild(labelHeureReport);
	divHeureReport.appendChild(selectHeureReport);
	
	var divSubmitReport = document.createElement("div");
	
	var inputSubmitReport = document.createElement("input");
	inputSubmitReport.setAttribute("name", "report");
	inputSubmitReport.setAttribute("type", "submit");
	inputSubmitReport.setAttribute("class", "boutons");
	inputSubmitReport.setAttribute("id", "submitReport");
	inputSubmitReport.setAttribute("value", "Reporter");
	
	divSubmitReport.appendChild(inputSubmitReport);
	
	formReport.appendChild(tokenInput);	
	formReport.appendChild(idSeanceInput);
	formReport.appendChild(divDateReport);
	formReport.appendChild(divHeureReport);
	formReport.appendChild(divSubmitReport);
	
	divReport.appendChild(divFlexQuitter);
	divReport.appendChild(formReport);
	
	divReport.setAttribute("class", "divReport");
	
	reports.appendChild(divReport);
	reports.style.opacity="1.0";
	reports.style.top=event.pageY;
	reports.style.left=event.pageX;
	
}
