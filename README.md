Asterisk Dialplan Tester
========================

This is just a proof-of-concept. It works on a very simple dialplan.

This dialplan:

```sh
[asterisktest-1]
exten => s,1,answer
same => n,wait(3)
same => n,goto(asterisktest-2,s,1)

[asterisktest-2]
exten => s,1,noop
same => n,Set(myvar=test)
same => n,wait(3)
same => n,goto(asterisktest-3,s,1)

[asterisktest-3]
exten => s,1,noop
same => n,waitexten(5)

exten => 1,1,goto(asterisktest-4,s,1)
exten => t,1,goto(asterisktest-5,s,1)
exten => i,1,goto(asterisktest-6,s,1)

[asterisktest-4]
exten => s,1,wait(3)
same => n,hangup

[asterisktest-5]
exten => s,1,wait(3)
same => n,hangup

[asterisktest-6]
exten => s,1,wait(3)
same => n,hangup
```

has been tested with this config file:

```sh
options:
    host: '127.0.0.1'
    port: 5038
    username: 'asterisktest'
    secret: 'asterisktest'
    connect_timeout: 30
    read_timeout: 30

before:
    - call:
        context: asterisktest-1
        extension: s
        priority: 1
        callerid: 35957457

after:


test_number1:
    - wait_context:
        context: asterisktest-1
        timeout: 30
    - assert_context: asterisktest-1
    - wait_context: asterisktest-3
    - assert_context: asterisktest-3
    - assert_variable: myvar=test
    - wait_context: asterisktest-5
    - assert_context: asterisktest-5
    - hangup: true
```

Producing this output:

```sh
Asterisk Dialplan Tester by Morten Amundsen.

....

1 test(s), 4/4 assert(s) succeeded, run in 17.386 seconds.
```
