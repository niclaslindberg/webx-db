# A simple Mysql PHP wrapper.
Main features of nillsoft-db

* Name based auto-escaped parametrization.
* Hierarchical transactions by using `savepoint X` and `rollback to savepoint X` in inner transactions.
* Key violation exception with name of violated key.

## How to use
```
    $db = new Impl\DbImpl($config);
    $db->execute("INSERT INTO table (colA,colB) VALUES(:a,:b)",[a=>"valueA",b="valueB"];

´´´


## How to run tests
In the root of the project `phpunit -c tests`.
