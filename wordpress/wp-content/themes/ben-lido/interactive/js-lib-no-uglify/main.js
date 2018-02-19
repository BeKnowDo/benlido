// setup simple element call
$ = function (_elem) { // single item query
    return document.querySelector(_elem);
};
$$ = function (_elem) { // multi-item query - creates array of class items
    return document.querySelectorAll(_elem);
};

// elements
var $burger = $('#burger-button'); // hamburger icon
var $magnify = $('#search-button'); // search icon
var $nav = $('#nav-menu'); // primary nav menu
var $search = $('#search-menu'); // search menu
var $$menu_close = $$('.menu-close'); // menu close icon
var $cover = $('#ui-cover'); // ui overlay

// menu toggle function - works for both primary nav and search menus
var $menu_active = false;
var $menu_item;
var toggleMenu = function (_menu) {
    switch ($menu_active) {
        case true:
            _menu.classList.remove('active');
            $cover.classList.remove('active');
            $menu_active = false;
            $menu_item = null;
        break;
        case false:
            _menu.className += 'active';
            $cover.className += 'active';
            $menu_active = true;
            $menu_item = _menu;
        break;
        default:
        //
        break;
    }
};

// actions
$burger.addEventListener('click', function () {
    toggleMenu($nav);
});
$magnify.addEventListener('click', function () {
    toggleMenu($search);
});
$$menu_close.forEach((_close, index) => {
    _close.addEventListener('click', function () {
        toggleMenu($menu_item);
    });
});
$cover.addEventListener('click', function () {
    toggleMenu($menu_item);
});