/**
 * @file
 * Custom commands to test provider book online dialogs.
 */

module.exports = {
    commands: [
        {
            openModalBookOnline: function () {

                return this
                    .assert.visible('@openModalCta', 'Provider make an appointment cta is visible.')
                    .click('@openModalCta')
                    .waitForElementVisible('@modalContainer', 'The modal is visible.')

            }
        }
    ],
    elements: {
        openModalCta: 'button.provider-page__book-online',
        modalContainer: '#modal-appointment-provider'
    }
};
