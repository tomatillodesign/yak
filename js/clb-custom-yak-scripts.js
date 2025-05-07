
// set light mode / dark mode / pink mode
document.addEventListener("DOMContentLoaded", function () {
  const modeKey = "site-theme-mode"; // Key for localStorage
  const body = document.body;
  const paintSelector = document.querySelector(".clb-paint-selector-menu-item");

  if (!paintSelector) return;

  // ✅ Define tray **before** any function uses it
  const tray = document.createElement("div");
  tray.classList.add("clb-color-mode-tray");
  

  // Function to update the tray dynamically
  function updateTray() {
      tray.innerHTML = ""; // Clear previous buttons
      let currentMode = localStorage.getItem(modeKey); // Get updated mode

      ["dark-mode", "light-mode", "colorful-mode"].forEach(mode => {
          if (mode === currentMode) return; // Hide the active mode button

          const button = document.createElement("button");
          button.innerHTML = '<span>' + mode.replace("-", " ").toUpperCase() + '</span>';
          if( mode == 'dark-mode' ) { button.innerHTML += '<span><i class="fa-solid fa-moon fa-lg"></i></span>'; }
          if( mode == 'light-mode' ) { button.innerHTML += '<span><i class="fa-solid fa-sun-bright fa-lg"></i></span>'; }
          if( mode == 'colorful-mode' ) { button.innerHTML += '<span><i class="fa-solid fa-sunglasses fa-lg"></i></span>'; }

          button.addEventListener("click", (e) => {
              e.stopPropagation();
              applyMode(mode); // Apply and refresh the menu
              tray.style.display = "none"; // Hide the tray
          });

          tray.appendChild(button);
      });
  }

  // Function to apply the selected mode
  function applyMode(mode) {
      body.classList.remove("dark-mode", "light-mode", "colorful-mode");
      body.classList.add(mode);
      localStorage.setItem(modeKey, mode);
      updateTray(); // Refresh the tray dynamically
  }

  // Function to determine the initial mode
  function getInitialMode() {
      let savedMode = localStorage.getItem(modeKey);

      if (!savedMode) {
          const prefersDark = window.matchMedia("(prefers-color-scheme: dark)").matches;
          const prefersLight = window.matchMedia("(prefers-color-scheme: light)").matches;

          if (prefersDark) {
              savedMode = "dark-mode";
          } else if (prefersLight) {
              savedMode = "light-mode";
          } else {
              savedMode = "dark-mode"; // Default
          }

          localStorage.setItem(modeKey, savedMode);
      }
      return savedMode;
  }

  // Get the initial mode and apply it
  let currentMode = getInitialMode();
  applyMode(currentMode);

  // Insert the tray into the menu item
  paintSelector.style.position = "relative";
  paintSelector.appendChild(tray);

  // Show tray on click
  paintSelector.addEventListener("click", (e) => {
      e.preventDefault();
      e.stopPropagation();
      updateTray(); // Refresh tray before displaying
      tray.style.display = "block";
  });

  // Hide tray when clicking anywhere outside of it
  document.addEventListener("click", () => {
      tray.style.display = "none";
  });
});




//////////////////


function moveModals() {

  // var x = document.createElement("P");                        // Create a <p> node
  // var t = document.createTextNode("This is a paragraph.");    // Create a text node
  // x.appendChild(t);                                           // Append the text to <p>
  // document.body.appendChild(x);                               // Append <p> to <body>

let modals = document.getElementsByClassName('clb-move-modals');
console.log(modals);

for (var i = 0; i < modals.length; i++) {
  //console.log(modals[i]);

  // copy and create new modal down in footer where the functionality will work
  let newModal = document.createElement("div");
  newModal.classList.add('clb-relocated-modal-wrapper');
  newModal.innerHTML = modals[i].innerHTML;
  document.body.appendChild(newModal);

  // wipe out the modal HTML in the entry-content
  modals[i].innerHTML = '';
}

}


moveModals();




function roundUpNearest10(num) {
  return Math.ceil(num / 25) * 25;
}


// Add 'scrolled' class to site container after 45px.
document.addEventListener("scroll", (event) => {
  const siteContainer = document.querySelector('.site-container');
  const scrollHeight = document.documentElement.scrollTop;
  const roundedHeight = roundUpNearest10(scrollHeight);
  document.body.setAttribute('data-scroll', scrollHeight);

  if( roundedHeight > 200 ) { siteContainer.classList.add( 'ironwood-scrolled' ) }
  else if( roundedHeight < 100 ) { siteContainer.classList.remove( 'ironwood-scrolled' ) }
  
});





// Find the search icon and replace the link with the call to the modal window
var enableSearch = function() {

if(!document.querySelector('.clb-custom-search-icon')) { return; }

  var el = document.querySelector('.clb-custom-search-icon').getElementsByTagName('a')[0];
  var search = '#site-search';

  el.href = search;
  el.title = 'Search this website';
  el.setAttribute("data-bs-toggle", "modal");
}

enableSearch();

