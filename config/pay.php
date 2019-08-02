<?php

return [
    'alipay' => [
        'app_id'         => '2016101000654909',
        'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEApHGXgobD0KMHnElGM4Ukg1Hp2JsVM5GIjzCdSJUlIE5zFIBG/HgpJJHZBD82/JrQCIBTEr/p57Qdo3QU0kX2O2XSqfPiOT3VaOff6ajb4VQ2ccqq8XU/n0ieAHA+/c2kHkAsJoLYiDaEsMLV49nL5lsLXD0bwTbKGYfMkS8MeRNSLjHvBTqZqnpMRX+q7phgH2Qb/lnwg3bU/sTxw5uPj2lNMTD6BfFXh+wpfK6gD7i5NSVxH0QPKzLxCLGf6SmaMko0a1RlLfjgEAk2Pid2Hra/DuRNuA2JZZLw/4LBmBA7gHFTdG1Z3nVN/anBgFb/++0ncNVDecGtLKpVdeJ++QIDAQAB',
        'private_key'    => 'MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCIZXHencRrlFNg0HFhCSkjXWwt7g/97KTFyLhg5QovLejntR1jmZIW9OaTd2qYhrBk+JAHMTZB3enmI9WybK6sqydu5sBcFCvgyvcfZuXCHrrutIc6osP9vpKZP1Y0eTIOeVVMOyUT2LESitQ9O5W6oH3+eDGNRceqn4XplMmFYqhGV55YvDDBvCCHaNUlruHJZnnZ8nYgECZBVG44+7EgJYtZnU0qECWa6eYZqEuhyyTM1v+VB+jheL7+6+e6yYpooPeqX47GiQy943/DAqD+6dKpF/fmsnkBTDD/lB8K0tgT62Dw/eGtRbsnLfp9jJCf4FLFOncqRiMjIaY5jg+hAgMBAAECggEBAIQi0ve5m8okvcmy5cP93e622NB+KtvyX4aEd6rW+NDQByxCF6Cf7Bl1U9NVAt2UzlpjOxTcphWVaL1dxe6+v1Hj1aXowj3yBLgXqOSxCLDQmIQcPE2f1YODFf6Ej3zyVH4VKX9aZQcfDBxMhmRzzxAn3yehRU/5Y/9134N0cu5WQ9ozNA+/vSwYInS5VpIC5Zm8SyxEAftpDCrPInmBP7W/gst8CqOPpC5+tNt/XJmblWf+HU+faK+WWitJOzih97rawPs/iBnoA4jExEV0D30RmSH+mchcbKus3AE8hiumdjrbHXMfMrrsFyUMt7IcoFnQYz4VZw8zogSe6sAXlmECgYEAzOpo2YGt0ccR8DcYZw+VN61D8109i0eZvIcXZo8zrKukMdNmqwuN1MnGcty5bCmWRi6q9LYHoj4uHJ9jya3DiEVQT2GXmW9uZy0c8wyHilY/YuDJxuW19tw5kgQWD4bCd8S7Keic/aQ4Fvc4MTg7Xb29ojqb9gJPI8NIMcuJOd0CgYEAqmYriDWHnx1qn74DXuuKCqL2MUMCnZN+KhneAvms0d89KgeiJUD1XIq1rpklpR7m8thAtsctJOzbjM+QF0T14NyKiQTm8aIJaFi4PU2ajz08wwoec67wkbE7RCVqmIsJeUNN4+W1JZK5Gno3ZOAcAUlOg74bW99iOR8fNIQzypUCgYBAhd8V/ftZfrFrTi3k5cc6jNhEnStv28/QyUjZBmZjDHjbJ5HAchbq1c4jBNVt9XpYBwHVpCurLqEeaiHls01Kb2jmVfiCW9ALtOzUqzFSoe27mMRwWIx/esC19YtswCYjyFKW06P7SeZPdPDArtAiqEg5+PJ6+CcrP6ZP56MCEQKBgCXLU4x6kwIvvB6CPq0nAQ8q5gA3JLVeqXUdF6kBR9uk8CQKXWR16/YCrhlzzm25VPA4FuJzewfdoTSyNPt0SDT/tZp+g9rEXeHPC85NECMFKhz4eZifDKzD6qlKw0HiVM+YpgYORQd4a6X7xZ2SN6PxZoDCAb925IpW5Mz5JaOhAoGAGL/vtiUe+K3fSEAbbcphbSXARCITGu/UQX5FV6Ie25juYfT+3sDghueN1z76jgLz4gsj5BtJEtJxqKi4eqDNg4Z0VmW0mUDerEpz7avLZexvGjuhQwtwzMh0zWyy/yTE1o7hKFDkyDL1KH7APdlBY9sTZiDY2gpTabZVMn4dYvE=',
        'log'            => [
            'file' => storage_path('logs/alipay.log'),
        ],
    ],

    'wechat' => [
        'app_id'      => '',
        'mch_id'      => '',
        'key'         => '',
        'cert_client' => '',
        'cert_key'    => '',
        'log'         => [
            'file' => storage_path('logs/wechat_pay.log'),
        ],
    ],
];
