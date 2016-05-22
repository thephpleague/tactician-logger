## 0.10 (2016-05-22)

New features:
- [#5](https://github.com/thephpleague/tactician-logger/pull/5): Rather than directly serializing command data into the message, the data is now piped to the log context parameter. This makes it much easier to parse for folks using more complex logging tools.

BC Breaks:
- Serializers have been replaced by Normalizers. This allows you to pipe the data into the log context in its original form.
- The Formatter interface has added two new methods for creating the log context (usually from a Normalizer)

