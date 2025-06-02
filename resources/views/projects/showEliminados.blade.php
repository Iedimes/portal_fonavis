<table border="1">
    <thead>
        <tr>
            <th>Postulante ID</th>
            <th>Nombre</th>
            <th>Apellido</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($postulantes as $postulante)
            <tr>
                <td>{{ $postulante->postulante_id ?? '[sin ID]' }}</td>
                <td>{{ optional($postulante->getPostulante)->first_name ?? '[sin nombre]' }}</td>
                <td>{{ optional($postulante->getPostulante)->last_name ?? '[sin apellido]' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
