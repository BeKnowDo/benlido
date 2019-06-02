const arguments = () => {
  let result;
  process.argv.forEach(function (argument) {
    if (argument === "--wordpress") {
      result = true;
    } else {
      result = false
    }
  });
  return result;
};

module.exports.check = () => arguments();
