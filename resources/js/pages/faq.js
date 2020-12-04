import {Page} from "./_page";

class Faq extends Page {

    faqSidebarList = document.getElementById('faq-sidebar-list');
    faqSidebarContent = document.getElementById('faq-sidebar-content');
    questions = document.querySelectorAll('.questions__item');

    constructor() {
        super();
        this.initSidebar();

        this.questions.forEach(question => {
            question.addEventListener('click', () => {
                question.classList.toggle('open')
            });
        });

    }

    initSidebar = () => {

        if(location.hash.length && location.hash.split("#")[1].length && document.getElementById(location.hash.split("#")[1])) {
            let target = document.getElementById(location.hash.split("#")[1]);

            this.faqSidebarContent.querySelectorAll('.faq-content').forEach(content => {
                content !== target ? content.style.display = 'none' : target.style.display = 'block'
            });

        }

        this.faqSidebarList.querySelectorAll('a[href]').forEach(link => {
            link.addEventListener('click',  () => {
                let href = link.href.split("#")[1];
                let target = document.getElementById(href);

                this.questions.forEach(question => {
                    question.classList.remove('open')
                });

                if(target) {

                    this.faqSidebarContent.querySelectorAll('.faq-content').forEach(content => {
                        content !== target ? content.style.display = 'none' : target.style.display = 'block'
                    });
                }
            })
        })
    }
}

new Faq();
