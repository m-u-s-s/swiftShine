<h2>Liste des rendez-vous</h2>
<table width="100%" border="1" cellpadding="4" cellspacing="0">
    <thead>
        <tr>
            <th>Date</th><th>Heure</th><th>Client</th><th>Employé</th><th>Statut</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $rdv)
            <tr>
                <td>{{ $rdv->date }}</td>
                <td>{{ $rdv->heure }}</td>
                <td>{{ $rdv->client->name ?? '—' }}</td>
                <td>{{ $rdv->employe->name ?? '—' }}</td>
                <td>{{ $rdv->status }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
