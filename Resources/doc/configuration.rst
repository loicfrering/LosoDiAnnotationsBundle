Configuration
=============

You can configure LosoBundle in one of the following ways in
app/config/config.yml::

    loso:
        service_scan:
            DemoBundle: ~
            MyBundle:
                base_namespace: [Prefix1, Prefix2\SubPrefix]
            arbitrary_key:
                dir:
                    - dir1
                    - dir2

