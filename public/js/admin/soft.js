/*!

=========================================================
* Soft UI Dashboard Tailwind - v1.0.2
=========================================================

* Product Page: https://www.creative-tim.com/product/soft-ui-dashboard-tailwind
* Copyright 2022 Creative Tim (https://www.creative-tim.com)
* Licensed under MIT (site.license)

* Coded by www.creative-tim.com

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

*/
var page = window.location.pathname.split("/").pop().split(".")[0];
var aux = window.location.pathname.split("/");
var to_build = (aux.includes('pages') ? '../' : './');
var root = window.location.pathname.split("/")
if (!aux.includes("pages")) {
  page = "dashboard";
}

loadStylesheet("/css/admin/perfect-scrollbar.css");
loadJS("/js/admin/perfect-scrollbar.js", true);

/* if (page != "sign-in" && page != "sign-up") { */
loadJS("/js/admin/fixed-plugin.js", true);
loadJS("/js/admin/sidenav-burger.js", true);
loadJS("/js/admin/dropdown.js", true);
/*   if (page != "profile") { */
loadJS("/js/admin/navbar-sticky.js", true);
/*  } else { */
loadJS("/js/admin/nav-pills.js", true);
/*   } */
/*   if (page != "tables") { */
loadJS("/js/admin/tooltips.js", true);
loadStylesheet("/css/admin/tooltips.css");
/*  } */
/* } else { */
loadJS("/js/admin/navbar-collapse.js", true);
/* } */


function loadJS(FILE_URL, async) {

  let dynamicScript = document.createElement("script");

  dynamicScript.setAttribute("src", FILE_URL);
  dynamicScript.setAttribute("type", "text/javascript");
  dynamicScript.setAttribute("async", async);

  document.head.appendChild(dynamicScript);
}

function loadStylesheet(FILE_URL) {
  let dynamicStylesheet = document.createElement("link");

  dynamicStylesheet.setAttribute("href", FILE_URL);
  dynamicStylesheet.setAttribute("type", "text/css");
  dynamicStylesheet.setAttribute("rel", "stylesheet");

  document.head.appendChild(dynamicStylesheet);
}
