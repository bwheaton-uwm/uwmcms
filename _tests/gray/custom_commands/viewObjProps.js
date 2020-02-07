module.exports = {
	command: async function(obj) {
		// viewObjProps()
		// dump a csv list of the object (browser) properties to console
		// Gray -- This is probably only useful to me, feel free to delete it after I'm gone.
		let props = [
		    this.capabilities,
		    this.currentTest,
		    this.desiredCapabilities,
		    this.sessionId,
		    this.options,
		    this.globals,
		    this.launchUrl,
		    this.launch_url,
		    this.Keys,
		    this.page,
		    this.assert,
		    this.verify,
		    this.expect,
		    this.acceptAlert,
		    this.back,
		    this.contexts,
		    this.cookie,
		    this.currentContext,
		    this.dismissAlert,
		    this.doubleClick,
		    this.element,
		    this.elementActive,
		    this.elementIdAttribute,
		    this.elementIdClear,
		    this.elementIdClick,
		    this.elementIdCssProperty,
		    this.elementIdDisplayed,
		    this.elementIdElement,
		    this.elementIdElements,
		    this.elementIdEnabled,
		    this.elementIdEquals,
		    this.elementIdLocation,
		    this.elementIdLocationInView,
		    this.elementIdName,
		    this.elementIdProperty,
		    this.elementIdSelected,
		    this.elementIdSize,
		    this.elementIdText,
		    this.elementIdValue,
		    this.elements,
		    this.execute,
		    this.executeAsync,
		    this.forward,
		    this.frame,
		    this.frameParent,
		    this.fullscreenWindow,
		    this.getAlertText,
		    this.getOrientation,
		    this.keys,
		    this.minimizeWindow,
		    this.mouseButtonClick,
		    this.mouseButtonDown,
		    this.mouseButtonUp,
		    this.moveTo,
		    this.openNewWindow,
		    this.refresh,
		    this.screenshot,
		    this.session,
		    this.sessionLog,
		    this.sessionLogTypes,
		    this.sessions,
		    this.setAlertText,
		    this.setContext,
		    this.setOrientation,
		    this.source,
		    this.status,
		    this.submit,
		    this.timeouts,
		    this.timeoutsAsyncScript,
		    this.timeoutsImplicitWait,
		    this.title,
		    this.url,
		    this.window,
		    this.windowHandle,
		    this.windowHandles,
		    this.windowMaximize,
		    this.windowPosition,
		    this.windowRect,
		    this.windowSize,
		    this.closeWindow,
		    this.deleteCookie,
		    this.deleteCookies,
		    this.end,
		    this.getCookie,
		    this.getCookies,
		    this.getLog,
		    this.getLogTypes,
		    this.getTitle,
		    this.getWindowPosition,
		    this.getWindowRect,
		    this.getWindowSize,
		    this.init,
		    this.injectScript,
		    this.isLogAvailable,
		    this.maximizeWindow,
		    this.pause,
		    this.perform,
		    this.resizeWindow,
		    this.saveScreenshot,
		    this.setCookie,
		    this.setWindowPosition,
		    this.setWindowRect,
		    this.setWindowSize,
		    this.switchWindow,
		    this.urlHash,
		    this.useCss,
		    this.useXpath,
		    this.clearValue,
		    this.click,
		    this.getAttribute,
		    this.getCssProperty,
		    this.getElementProperty,
		    this.getElementSize,
		    this.getLocation,
		    this.getLocationInView,
		    this.getTagName,
		    this.getText,
		    this.getValue,
		    this.isVisible,
		    this.moveToElement,
		    this.setValue,
		    this.sendKeys,
		    this.submitForm,
		    this.waitForElementNotPresent,
		    this.waitForElementNotVisible,
		    this.waitForElementPresent,
		    this.waitForElementVisible,
		    this.runtimeLog,
		    this.searchProvidersGetRandomCard,
		    this.searchProvidersGetRandomPage,
		    this.viewObjProps,
		    this.isES6Async
		];
		Object.getOwnPropertyNames(this).forEach(function (val, idx, arr){
			let i = idx;
			console.log(val + ",(" + typeof(props[i]) + ")");
			if (val === "desiredCapabilities") {
				console.log("skipped " + val);
			} else {
				Object.getOwnPropertyNames(props[i]).forEach(function (val, idx, arr){
					console.log("....." + "," + val);
				})
			}
		});
		return this;
	}
};
