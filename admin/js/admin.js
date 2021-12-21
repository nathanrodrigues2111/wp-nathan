const commenAdminForm = (commentform) => {
    commentform.addEventListener(
        'submit', (e) => {
        e.preventDefault();
        const selectElement = document.querySelector('#default_comments_theme');
        const currentSelectedValue = selectElement.options[selectElement.selectedIndex].value;
        const enableComments = ajax_admin.enableComments;
            if (currentSelectedValue === 'regular' || currentSelectedValue === 'modern') {
                commentformSubmitButton = document.querySelector('#submit');
                commentformSubmitButton.setAttribute('value', 'Saving...');

                fetch(
                ajaxurl, {
                    method: 'POST',
                    body: new URLSearchParams(
                    {
                        action: 'admin_hook',
                        selected_theme: currentSelectedValue,
                        enable_comments: enableComments,
                    }
                )
                }
                ).then(
                (res) => {
                    commentformSubmitButton.setAttribute('value', 'Save Settings');
                }
                )
            }
        }
    );
}

const enableAdminSection = (trigger, target) => {
    trigger.addEventListener(
        'change', function () {
            if (this.checked) {
                target.classList.remove('hide-target');
                ajax_admin.enableComments = 'true';
            } else {
                target.classList.add('hide-target');
                ajax_admin.enableComments = 'false';
            }
        }
    );
}

document.addEventListener(
    "DOMContentLoaded", function () {
        const commentform = document.querySelector('.ajax-form');
        const triggerToggle = document.querySelector("input[name='enable_comment_styles']");
        const targetToggle = document.querySelector('.toggle-comment-settings');

        if (commentform) {
            commenAdminForm(commentform);
            enableAdminSection(triggerToggle, targetToggle);
        }
    }
);

