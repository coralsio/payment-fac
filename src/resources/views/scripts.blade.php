<script type="text/javascript">
    function onlyNumbers(value) {
        return value.replace(/[^0-9]/g, '');
    }

    $(document).on('keyup', '#fac_cvv, #fac_number', function (event) {
        $(this).val(onlyNumbers($(this).val()));
    });
</script>
