## 0.10 (2016-05-22)

This release has a number of BC breaks and refactorings. Specifically:

- The Formatter interface has been completely reworked to allow direct access to the logger (more below).
- Serializers have been replaced by Normalizers. This allows you to pipe the data into the log context in its original form.

The old interface for a formatter allowed you to return strings which were then passed to the logger for you. This was the simplest interface but it didn't allow using a number of advanced PSR-3 features like contexts or conditionally changing the log level based on the type of command or exception.

The new Formatter interface passes the logger to you directly so you can log yourself in any way you choose. We've also changed the Serializer to a Normalizer. This might seem tiny but the difference is important: rather than slamming objects down into strings, we now convert them to arrays so they can be passed to the PSR-3 context. This is awesome if you're using more powerful log aggregation/search tools.

We still ship with a couple of simple Formatters built in. These are great examples of how to build your own if you need really advanced logging features and we recommend writing your own instead of adding more features to these.

There's one last change: because the actual logging is now done in Formatters instead of the middleware, you now pass the default log levels to the Formatter rather than the middleware. This is only a convenience for our built-in ones to get you a little further down the road, you don't need to do this for your own formatters.

For more informations:
- [#5](https://github.com/thephpleague/tactician-logger/pull/5): Rather than directly serializing command data into the message, the data is now piped to the log context parameter. This makes it much easier to parse for folks using more complex logging tools.
- [#7](https://github.com/thephpleague/tactician-logger/pull/7): Further reworks inspired by a review of #5.
