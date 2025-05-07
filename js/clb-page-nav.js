
// create new sidebar using the block items from the page template (if they exist)
const setupSidebarNav = function() {

    console.log('setupSidebarNav 331p');
    const sidebarNavItems = document.querySelectorAll('.clb-on-page-nav-link');
    console.log(sidebarNavItems);

    // clb-on-page-nav-wrapper
    // get the entry header
    const entryHeader = document.querySelector('.entry-header');
    
    // Create a new element
    var sidebarNav = document.createElement('div');
    sidebarNav.classList.add('clb-on-page-nav-wrapper');
    var sidebarNavUl = document.createElement('ul');

    const sidebarLinks = document.querySelectorAll('.clb-custom-nav-item');
    //console.log(sidebarLinks);

    sidebarLinks.forEach((sidebarLink) => {
         console.log(sidebarLink);
         sidebarNavUl.appendChild(sidebarLink);
    });

    // Insert the new node before the reference node
    entryHeader.after(sidebarNav);
    sidebarNav.appendChild(sidebarNavUl);



}
setupSidebarNav();









const setupSidebarClicks = function() {

          console.log('setupSidebarClicks 321p');
          const sidebarNavItems = document.querySelectorAll('.clb-on-page-nav-link');
          console.log(sidebarNavItems);

          sidebarNavItems.forEach((sidebarNavItem) => {
               sidebarNavItem.addEventListener('click', () => {

                    if (window.matchMedia("(min-width: 960px)").matches) {

                         event.preventDefault();

                         // remove url hash, if it exists
                         const hash = window.location.hash;
                         //if hash/direct link exists
                         if( hash.length > 0 ) {
                              window.location.hash = null;
                              history.pushState("", document.title, window.location.pathname
                                                       + window.location.search);
                         }

                         for (i = 0; i < sidebarNavItems.length; i++) {
                              sidebarNavItems[i].classList.remove('clb-sidebar-item-active');
                         }
                         sidebarNavItem.classList.add('clb-sidebar-item-active');

                         // hide all items
                         const pageTabs = document.querySelectorAll('.page-tab');
                         for (i = 0; i < pageTabs.length; i++) {
                              let currentPageTab = pageTabs[i];
                              currentPageTab.classList.add('clb-hidden-page-tab');
                         }

                         //get item on page and SHOW IT
                         const tabLink = sidebarNavItem.dataset.tabLink;
                         console.log(tabLink);
                         // const pageTabSection = document.querySelector('#' + tabLink);
                         const pageTabSection = document.querySelector(tabLink);
                         console.log(pageTabSection);

                         pageTabSection.classList.remove('clb-hidden-page-tab');

                         //update 9/15/22
                         console.log("9-15 UPDATE - Scrolling 334p");
                         window.scrollTo(0, 200);

                    }

               });
          });

}
setupSidebarClicks();




const setupPageNav = function() {

    console.log('setupPageNav 318p');
    const pageTabs = document.querySelectorAll('.page-tab');
    const hash = window.location.hash;
    console.log(pageTabs);

    //if( pageTabs.length < 1 ) { return; }

    for (i = 0; i < pageTabs.length; i++) {

          let currentPageTab = pageTabs[i];
          currentPageTab.classList.add('clb-hidden-page-tab');

          const currentTabHashID = '#' + pageTabs[i].id;
          console.log(currentTabHashID);

          if( hash.length > 0 ) {

               if( currentTabHashID == hash ) { 
                    console.log("HASH TRUE TABLINK");
                    currentPageTab.classList.remove('clb-hidden-page-tab');
               }

          } else {
               if( currentTabHashID != hash && i === 0 ) { 
                         console.log("FIRST ITEM");
                         currentPageTab.classList.remove('clb-hidden-page-tab'); 
                    }
          }
          // else if( currentTabHashID != hash && i === 0 ) { 
          //      console.log("FIRST ITEM");
          //      currentPageTab.classList.remove('clb-hidden-page-tab'); 
          // }

    }

    const sidebarNavItems = document.querySelectorAll('.clb-on-page-nav-link');
    
    //if hash/direct link exists
    if( hash.length > 0 ) {

          for (i = 0; i < sidebarNavItems.length; i++) {
               //console.log(sidebarNavItems[i].hash);
               if( sidebarNavItems[i].hash == hash ) {
                    sidebarNavItems[i].classList.add('clb-sidebar-item-active');
               }
          }

    } else {

          for (i = 0; i < sidebarNavItems.length; i++) {
               if( i == 0 ) { sidebarNavItems[i].classList.add('clb-sidebar-item-active'); }
          }

     }

}

setupPageNav();



window.addEventListener("resize", function() {
     setupSidebarClicks();
     setupPageNav();
   });