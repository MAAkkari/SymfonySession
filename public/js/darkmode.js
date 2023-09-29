document.addEventListener('DOMContentLoaded', function() {
    const dark_btn = document.querySelector('.dark');
    const body = document.querySelector('body');
    const nav = document.querySelector('nav');
    const head =document.querySelector('header');
    const header = document.querySelectorAll('header nav ul li');
    const formulaire = document.querySelectorAll('.form_session');
    const formInput = document.querySelectorAll('.form_session input');
    const table = document.querySelectorAll('table tbody tr');
    const lists = document.querySelectorAll('div li a');
    const labels = document.querySelectorAll('main table tbody tr td p');
    const h3 = document.querySelectorAll('h3,h2, h1');
    const links = document.querySelectorAll('header nav ul li a');
    const rightLinks = document.querySelectorAll('.right_nav li a');
    const wrapper = document.querySelectorAll('.tab_wrapper');
    const trs = document.querySelectorAll('th');
    const label=document.querySelectorAll('label');

    body.classList.remove('preload');
    //check si l'item darkmode est dans le localstorage
    if (localStorage.getItem('darkMode') === 'true') {{
        body.classList.add('darkmode');
    }
        const changes = [...label,...trs,dark_btn,body, head , nav, , ...header,...links, ...formulaire, ...formInput, ...table,...rightLinks, ...lists,...wrapper, ...labels, ...h3];
        changes.forEach(function(elements) {
            elements.classList.add('darkmode');
        });}
    dark_btn.addEventListener('click', function() {
        const togglable = [...label,...trs,...wrapper,dark_btn,body,head, nav, , ...header,...links, ...formulaire, ...formInput, ...table,...rightLinks, ...lists, ...labels, ...h3 ];

        togglable.forEach(function(elements) {
            elements.classList.toggle('darkmode');
        });
        localStorage.setItem('darkMode', body.classList.contains('darkmode')); });  
});