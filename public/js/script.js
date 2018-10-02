/* ------------------- GLOBAL ---------------------*/
let y = 0.1;
let x = 0.1;

let timer = setInterval(affiche, 50);

function affiche() {
  let mainOpacity = document.getElementsByTagName("body")[0];

  if (mainOpacity.style.opacity < 1) {
    y += x;
    mainOpacity.style.opacity = y;
  } else {
    clearInterval(timer);
  }
}

// Mise en avant de l'article au passage de la souris
let card = document.querySelectorAll("section .card");
let cardTitle = document.querySelectorAll("section .card-title");
let lireSuite = document.querySelectorAll("section .lireSuite");

if (card.length > 0) {
  for (let i = 0; i < card.length; i++) {
    card[i].addEventListener("mouseover", function() {
      cardTitle[i].style.textDecoration = "underline";
      lireSuite[i].style.textDecoration = "underline";
      //card[i].style.border = "1px #119B15 solid";
      card[i].style.marginTop = "10px";
      card[i].style.transition = "all 0.3s ease-out";
    });

    card[i].addEventListener("mouseout", function() {
      cardTitle[i].style.textDecoration = "none";
      lireSuite[i].style.textDecoration = "none";
      card[i].style.border = "none";
      card[i].style.margin = "0";
    });
  }

  let rndCard = document.querySelectorAll("#randomArticle .card");
  let rndCardTitle = document.querySelectorAll("#randomArticle .card-title");
  let rndLireSuite = document.querySelectorAll("#randomArticle .lireSuite");

  for (let i = 0; i < rndCard.length; i++) {
    rndCard[i].addEventListener("mouseover", function() {
      rndCardTitle[i].style.textDecoration = "underline";
      rndLireSuite[i].style.textDecoration = "underline";
    });

    card[i].addEventListener("mouseout", function() {
      rndCardTitle[i].style.textDecoration = "none";
      rndLireSuite[i].style.textDecoration = "none";
    });
  }
}

/* ------------------- HEADER SECTION ---------------------*/
let navBarRight = document.querySelectorAll("header .navbar-nav")[1];

if (window.innerWidth < 768) {
  navBarRight.classList.remove("navbar-right");
  navBarRight.style.fontSize = "0.8rem";
}

window.addEventListener("resize", () => {
  if (window.innerWidth > 768) {
    navBarRight.classList.add("navbar-right");
  } else {
    navBarRight.classList.remove("navbar-right");
  }
});

/* ------------------- COMMENTAIRES SECTION ---------------------*/
let btnShowComm = document.querySelectorAll(".bodyCommentaires .showComm");
let textComm = document.querySelectorAll(".textComm");

if (btnShowComm.length > 0) {
  for (let i = 0; i < btnShowComm.length; i++) {
    btnShowComm[i].addEventListener("click", function() {
      btnShowComm[i].style.display = "none";
      textComm[i].style.display = "inline";
    });
  }
}

/* ------------------- ADMIN SECTION ---------------------*/
let adminContainer = document.querySelector(".container");

if (document.location.pathname.search(/admin/g) === 1) {
  adminContainer.style.maxWidth = "90%";
}
