const error = document.getElementById('error');
const errorList = document.getElementById('errorList');

const form = document.getElementById('form');
const username = document.getElementById('username')
const password = document.getElementById('password')
const mail = document.getElementById('mail')
const methodSend = document.getElementById('methodInput')
const freeID = document.getElementById('freeID')
const APIkey = document.getElementById('APIkey')

let mail_regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

function mask_onglet() {
  const tabcontent = document.getElementsByClassName("tabcontent");
  for (let i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }

  const tablinks = document.getElementsByClassName("tablinks");
  for (let i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }
}

function openTab(evt, tabName) {
  mask_onglet()

  document.getElementById(tabName).style.display = "flex";
  
  if (evt) {
    evt.currentTarget.className += " active";
  }

  methodSend.value = tabName;
}

mask_onglet();
openTab(event, 'discord')

function add_error(error_text) {
  const li = document.createElement('li');
  li.appendChild(document.createTextNode(error_text));
  errorList.appendChild(li);
}

async function verif_exsists(key, value) {
  const reponse = await fetch('https://nathanaelle.alwaysdata.net/become-a-loser/verif-info.php?key=' + encodeURIComponent(key) +  '&value=' + encodeURIComponent(value));
  const data = await reponse.json();
  return data['exists'];
}

form.addEventListener('submit', async function (event) {
  event.preventDefault();

  errorList.innerHTML = "";
  error.style.display = "none";

  if (username.value == "") {
    add_error('Le nom d\'utilisateur ne peut être vide.');
  }

  else if (await verif_exsists('username', username.value) & methodSend.value != 'discord') {
    add_error('Le nom d\'utilisateur est déjà pris');
  }

  if (password.value.length < 8) {
    add_error('Le mot de passe doit contenire au moin 8 caractères')
  }

  if (!mail_regex.test(mail.value)) {
    add_error('Le mail n\'est pas valide')
  }

  if (methodSend.value == 'free') {
    if (freeID.value == "") {
      add_error('FreeID ne peut pas être vide')
    }
    else if (await verif_exsists('freeID', freeID.value)) {
      add_error('ID Free déjà inscrit')
    }
    if (APIkey.value == "") {
      add_error('La clé d\'API ne peut être nul')
    }
  }

  if (errorList.innerHTML != "") {
    error.style.display = "block";
  } else {
    form.submit();
  }

});
