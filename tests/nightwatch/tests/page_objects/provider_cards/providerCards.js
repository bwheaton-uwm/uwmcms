/**
 * @file
 * Custom commands to test provider book online dialogs.
 */

module.exports = {
    commands: [
        {
            openBookOnlineModal: function () {

                return this
                    .assert.visible('@openModalCta', 'Provider make an appointment cta is visible.')
                    .click('@openModalCta')
                    .waitForElementVisible('@modalContainer', 'The modal is now visible.')

            },

            closeBookOnlineModal: function () {

                return this.browser
                    .element('css selector', '@openModalCta', result => {

                        if (result.status > -1) {

                            this.browser.click('@modalContainer')
                            this.browser.assert(true, 'Clicking to close the Book Online  modal.')

                        }
                        else {

                            this.browser.assert(true, 'The modal is already closed.')

                        }
                    });
            },

            testModalDimensionsAndColor: function () {

                this.browser
                    .waitForElementVisible(this.elements.modalBody, 'The modal body is visible.')

                    .getText(this.elements.modalBody, result => {
                        console.log('The body text is: ', result.value);
                    })

                    .getElementSize('css selector', this.elements.modalBody, result => {

                        this.browser.assert.ok((result.value.width > 400), 'The modal body is at least 400px wide.')
                        this.browser.assert.ok((result.value.height > 100), 'The modal body is at least 100px tall.')

                    })

                    .expect.element(this.elements.modalHeader).to.have.css('background-color').which.equals('rgba(44, 106, 206, 1)')

            },

            testBookOnlineDirectScheduling: function () {

                let buttonOptions = [];

                this.browser.expect.element(this.elements.modalBody).text.to.contain('Have you visited this provider within');

                this.browser.elements('css selector', this.elements.modalButton, result => {

                    for (var el of result.value) {
                        this.browser.elementIdText(el.ELEMENT, text => {
                            if (text.value.length > 0) {
                                buttonOptions[text.value] = el.ELEMENT
                            }
                        })

                    }

                }).perform(() => {
                    console.log('perform buttonOptions', buttonOptions);
                    this.browser.assert.ok(buttonOptions.hasOwnProperty('Yes'), 'The have you visited dialog includes a "Yes" button.')
                    this.browser.assert.ok(buttonOptions.hasOwnProperty('No'), 'The have you visited dialog includes a "No" button.')
                    this.browser.assert.ok(Object.keys(buttonOptions).length == 2, 'The have you visited dialog includes only two choices.')
                })

            }

        }


    ],
    elements: {
        openModalCta: 'button.provider-page__book-online',
        modalContainer: '#modal-appointment-provider',
        modalHeader: '#modal-appointment-provider .modal-header',
        modalBody: '#modal-appointment-provider .modal-body',
        modalButton: '#modal-appointment-provider a.btn-cta-solid',
        modalHeaderColor: 'rgba(44, 106, 206, 1)'
    }
};
