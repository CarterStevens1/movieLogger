import './bootstrap';


window.addEventListener('DOMContentLoaded', () => {
    let Main = document.getElementById("navigation");
    let body = document.getElementsByTagName("body")[0];
    let bodyWrapper = document.getElementById("wrapper");
    let openIcon = document.getElementById("openIcon");
    let closeIcon = document.getElementById("closeIcon");

  function showNav() {
        openIcon.classList.toggle("hidden");
        closeIcon.classList.toggle("hidden");
        Main.classList.toggle("-translate-x-full");
        Main.classList.toggle("max-xl:hidden");
        Main.classList.toggle("translate-x-0");
        Main.classList.toggle("min-w-full");
        Main.classList.toggle("h-[calc(100vh_-_82px)]!");
        body.classList.toggle("overflow-hidden");
        bodyWrapper.classList.toggle("overflow-hidden");


    };
    openIcon.addEventListener('click', showNav);
    closeIcon.addEventListener('click', showNav);
});
