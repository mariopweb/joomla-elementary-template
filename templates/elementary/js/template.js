document.addEventListener('DOMContentLoaded', function () {
    const navList = document.querySelectorAll('.navbar .nav');

    navList.forEach(ul => {
        // 1 level?: check for parent
        if (ul.firstElementChild.classList.contains('parent')) {
            ul.firstElementChild.parentElement.classList.contains('flex-column') ? ul.firstElementChild.classList.add('dropright') : ul.firstElementChild.classList.add('dropdown');
            let level1items = ul.firstElementChild.children;
            level1items[0].classList.add('dropdown-toggle');
            level1items[1].classList.add('dropdown-menu');
            for (let i = 0; i < level1items[1].childElementCount; i++) {
                level1items[1].children[i].classList.add('dropdown-item');
                // 2 level?: check for parent
                if (level1items[1].children[i].classList.contains('parent')) {
                    level1items[1].children[i].classList.add('dropright');
                    let level2items = level1items[1].children[i].children
                    level2items[0].classList.add('dropdown-toggle');
                    level2items[1].classList.add('dropdown-menu');
                    for (let k = 0; k < level2items[1].childElementCount; k++) {
                        level2items[1].children[k].classList.add('dropdown-item');
                        // 3 level?: check for parent
                        if (level2items[1].children[k].classList.contains('parent')) {
                            level2items[1].children[k].classList.add('dropright');
                            let level3items = level2items[1].children[k].children
                            level3items[0].classList.add('dropdown-toggle');
                            level3items[1].classList.add('dropdown-menu');
                            for (let j = 0; j < level3items[1].childElementCount; j++) {
                                level3items[1].children[j].classList.add('dropdown-item');
                            }
                        }
                    }
                }
            }
        }
    })
})