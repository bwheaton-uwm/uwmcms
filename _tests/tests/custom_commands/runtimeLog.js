module.exports = {
	command: async function(msg) {
		// runtimeLog()
		// Exactly like console.log(), except it should run as the test case executes, instead of
		// when immediate, during the command queue being built.
		console.log(msg);
		return this;
	}
};