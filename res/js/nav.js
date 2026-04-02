const header = document.getElementsByTagName('header')[0]
const path = window.location.pathname;

const links = ["/", "/become-a-loser/"];
const tag = ["Accueil", "Become a loser"]

links.forEach((url, index) => {
  link = document.createElement('a')
  link.classList.add('nav')
  if (path == url) {
    link.classList.add('here')
  }
  link.href = url
  link.innerText = tag[index]
  header.appendChild(link)
});