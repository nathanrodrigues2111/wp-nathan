
if (pagenow = 'toplevel_page_wpnat-settings' ) {

    const commentform = document.querySelector( '.ajax-form' );

    if( commentform ) {

        commentform.addEventListener( 'submit', (e) => {
            e.preventDefault();

            const selectElement = document.querySelector( '#default_comments_theme' );
            const currentSelectedValue = selectElement.options[selectElement.selectedIndex].value;

            if( currentSelectedValue === 'regular' || currentSelectedValue === 'modern' || currentSelectedValue === 'classic' ) {
                commentformSubmitButton = document.querySelector('#submit');
                commentformSubmitButton.setAttribute('value', 'Saving...');

                fetch( ajaxurl, {
                    method: 'POST',
                    body: new URLSearchParams( {
                        action: 'admin_hook',
                        selected_theme: currentSelectedValue,
                    } )
                } ).then( ( res ) => { 
                    commentformSubmitButton.setAttribute('value', 'Save Settings');
                } )
            }

        });
    }
    
}

