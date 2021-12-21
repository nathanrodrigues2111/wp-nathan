const getParentCommentCount = () => {
    const currentParentTarget = document.querySelectorAll('.depth-1.parent');
    if (currentParentTarget.length) {
        currentParentTarget.forEach(single => {
            currentSingleCount = single.querySelector('.last').querySelector('.wpnat-count').textContent;
            single.querySelector('.wpnat-comments-count').textContent = currentSingleCount;
        });
    }
}

document.addEventListener(
    "DOMContentLoaded",
    function () {
        const hideChildrenComments = document.querySelectorAll('.wpnat-comment-body > .wpnat-comment-arrow-trigger');

        if (hideChildrenComments.length) {
            hideChildrenComments.forEach(element => {
                element.addEventListener('click', (e) => {
                    e.currentTarget.parentElement.parentElement.classList.toggle('hide')
                });
            });
        }

        getParentCommentCount();
    }
);
