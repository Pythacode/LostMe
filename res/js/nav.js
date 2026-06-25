const header = document.getElementsByTagName('header')[0]
const path = window.location.pathname;

const links = ["/", "/become-a-loser/", "/mentions-legales/"];
const tag = ["Accueil", "Become a loser", "Mentions légales"]

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