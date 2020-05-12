document.addEventListener('DOMContentLoaded', function () {
    const navList = document.querySelectorAll('.navbar .nav');

    navList.forEach(ul => {

        // 1 level?: check for parent
        for (let i = 0; i < ul.children.length; i++) {
            if (ul.children[i].classList.contains('parent')) {
                ul.classList.contains('flex-column') ? ul.children[i].classList.add('dropright') : ul.children[i].classList.add('dropdown');
                let level1items = ul.children[i].children
                for (let j = 0; j < level1items[1].childElementCount; j++) {
                    level1items[0].classList.add('dropdown-toggle');
                    level1items[1].classList.add('dropdown-menu', 'dropdown-menu-right');
                    for (let k = 0; k < level1items[1].childElementCount; k++) {
                        level1items[1].children[k].classList.add('dropdown-item');
                        //  2 level?: check for parent
                        if (level1items[1].children[k].classList.contains('parent')) {
                            level1items[1].children[k].classList.add('dropright');
                            let level2items = level1items[1].children[k].children;
                            level2items[0].classList.add('dropdown-toggle');
                            level2items[1].classList.add('dropdown-menu');
                            for (let m = 0; m < level2items[1].childElementCount; m++) {
                                level2items[1].children[m].classList.add('dropdown-item');
                                // 3 level?: check for parent
                                if (level2items[1].children[m].classList.contains('parent')) {
                                    level2items[1].children[m].classList.add('dropright');
                                    let level3items = level2items[1].children[m].children;
                                    level3items[0].classList.add('dropdown-toggle');
                                    level3items[1].classList.add('dropdown-menu');
                                    for (let n = 0; n < level3items[1].childElementCount; n++) {
                                        level3items[1].children[n].classList.add('dropright')
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    })
})