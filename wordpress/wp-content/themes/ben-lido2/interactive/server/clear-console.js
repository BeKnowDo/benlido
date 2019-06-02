const clear = () => {
  return process.stdout.write("\033c");
};

module.exports.clear = () => clear();
