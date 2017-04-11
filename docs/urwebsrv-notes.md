PHP 5.2.9 Deployment checklist

1. No array shorthand syntax.

    Use `array(...)` instead of `[...]`

2. No namespacing support.

    Instead of `use Namespace\Class`, you need to:

    1. remove all cases of `namespace` and `use` statements
    2. rename your classes from `class_file` to `folder1_folder2_class_file` (or similar)
    3. use $sample = new folder1_folder2_class_file($arg1, $arg2);

    [craaaaazy.](http://stackoverflow.com/questions/7084872/php-5-2-new-and-use-keyword-path-problem)

    ex.

    ```
    use UAParser\Parser; -> require_once()
    ```

3. Cannot use `__DIR__`.

Use `APPATH` instead. It's equivalent to: `'application/'`.
