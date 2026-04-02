const input = document.getElementById('name');
const output = document.getElementById('name-visualizer');

const error = document.getElementById('error');
const errorList = document.getElementById('errorList');

const form = document.getElementById('form');
const loser = document.getElementById('loser');

const datalist = document.getElementById('datalist');
const datalistChildren = datalist.children;

var output_value = ""
var loser_value = ""
let count_prop = 0

input.addEventListener('input', function() {
    if (this.value == "") {
        output_value = "Quelqu'un"
    } else {
        output_value = this.value
    }
    output.innerHTML = output_value 
});

function add_error(error_text) {
  const li = document.createElement('li');
  li.appendChild(document.createTextNode(error_text));
  errorList.appendChild(li);
}

function check_datalist() {
  loser_value = loser.value
  for (var i = 0; i < datalistChildren.length; i++) {
    if (datalistChildren[i].innerHTML.toLowerCase().includes(loser_value.toLowerCase())) {
      datalistChildren[i].style.display = "block";
      count_prop += 1;
    } else {
      datalistChildren[i].style.display = "none";
    }

    if (count_prop == 0) {
      datalist.style.display = "none";
    } else {
      datalist.style.display = "flex"
    }
  }
}

function complete(value) {
  loser.value = value
  datalist.style.display = "none";
}


loser.addEventListener('input', check_datalist)
loser.addEventListener('click', check_datalist)
loser.addEventListener('blur', function (event) {
  const divAutorise = document.getElementById('datalist');
  const cible = event.relatedTarget;
  if (cible && divAutorise.contains(cible)) {
    return;
  }
  datalist.style.display = "none";
});

async function verif_exsists(key, value) {
  const reponse = await fetch('https://nathanaelle.alwaysdata.net/become-a-loser/verif-info.php?key=' + encodeURIComponent(key) +  '&value=' + encodeURIComponent(value));
  const data = await reponse.json();
  return data['exists'];
}

form.addEventListener('submit', async function (event) {
  event.preventDefault();

  errorList.innerHTML = "";
  error.style.display = "none";

  if (!(await verif_exsists('username', loser.value)) & loser.value != "") {
    add_error('L\'utilisateur est inconnus');
  }

  if (errorList.innerHTML != "") {
    error.style.display = "block";
  } else {
    form.submit();
  }

});
